import yfinance as yf
import pymysql
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv
import sys

# Load environment variables
load_dotenv()

class GoldDataUpdater:
    def __init__(self):
        self.db_config = {
            'host': os.getenv('DB_HOST', 'localhost'),
            'port': int(os.getenv('DB_PORT', '3306')),
            'user': os.getenv('DB_USERNAME', 'root'),
            'password': os.getenv('DB_PASSWORD', ''),
            'database': os.getenv('DB_DATABASE', 'db_prediksi_emas'),
            'charset': 'utf8mb4',
            'connect_timeout': 10,
            'read_timeout': 30,
            'write_timeout': 30
        }
        self.kurs_usd_to_idr = 16650

    def get_db_connection(self):
        """Membuat koneksi ke database"""
        try:
            print("[INFO] Mencoba koneksi ke database...")
            print(f"[INFO] Host: {self.db_config['host']}")
            print(f"[INFO] Port: {self.db_config['port']}")
            print(f"[INFO] User: {self.db_config['user']}")
            print(f"[INFO] Database: {self.db_config['database']}")

            conn = pymysql.connect(**self.db_config)

            # Test koneksi
            with conn.cursor() as cursor:
                cursor.execute("SELECT 1")
                cursor.fetchone()

            print("[SUCCESS] Koneksi database berhasil")
            return conn

        except pymysql.MySQLError as err:
            print(f"\n[ERROR] Error koneksi database MySQL:")
            print(f"[ERROR] {err}")

            error_msg = str(err).lower()
            if 'access denied' in error_msg:
                print("\n[TIP] Solusi:")
                print("[TIP] - Cek username dan password di file .env")
                print("[TIP] - Pastikan user memiliki akses ke database")
            elif 'unknown database' in error_msg:
                print("\n[TIP] Solusi:")
                print("[TIP] - Database belum dibuat")
                print("[TIP] - Jalankan: CREATE DATABASE db_prediksi_emas;")
            elif 'can\'t connect' in error_msg or 'connection refused' in error_msg:
                print("\n[TIP] Solusi:")
                print("[TIP] - Pastikan MySQL service running")
                print("[TIP] - Cek apakah port 3306 sudah benar")

            sys.exit(1)

        except Exception as e:
            print(f"\n[ERROR] Error tidak terduga: {e}")
            import traceback
            traceback.print_exc()
            sys.exit(1)

    def fetch_gold_price(self, start_date=None, end_date=None):
        """Mengambil data harga emas dari Yahoo Finance"""
        try:
            print(f"\n[INFO] Mengambil data dari Yahoo Finance...")
            gold_ticker = yf.Ticker("GC=F")

            if not start_date:
                start_date = (datetime.now() - timedelta(days=365*5)).strftime('%Y-%m-%d')

            if not end_date:
                end_date = datetime.now().strftime('%Y-%m-%d')

            print(f"[INFO] Periode: {start_date} sampai {end_date}")

            hist = gold_ticker.history(start=start_date, end=end_date)

            if hist.empty:
                print("[ERROR] Tidak ada data yang ditemukan dari Yahoo Finance")
                return None

            print(f"[SUCCESS] Berhasil mengambil {len(hist)} baris data")
            print(f"[INFO] Rentang: {hist.index[0].strftime('%Y-%m-%d')} s/d {hist.index[-1].strftime('%Y-%m-%d')}")
            print(f"[INFO] Harga terakhir: ${hist['Close'].iloc[-1]:.2f} per troy ounce")

            return hist

        except Exception as e:
            print(f"[ERROR] Error mengambil data: {e}")
            import traceback
            traceback.print_exc()
            return None

    def calculate_percentage_change(self, current_price, previous_price):
        """Menghitung persentase perubahan harga"""
        if previous_price == 0:
            return 0
        return ((current_price - previous_price) / previous_price) * 100

    def convert_usd_to_idr_per_gram(self, price_usd_per_oz):
        """Konversi harga dari USD/oz ke IDR/gram"""
        # 1 troy ounce = 31.1 gram
        price_usd_per_gram = price_usd_per_oz / 31.1
        price_idr_per_gram = price_usd_per_gram * self.kurs_usd_to_idr
        return round(price_idr_per_gram, 2)

    def check_existing_data(self, conn, date_str):
        """Cek apakah data sudah ada di database"""
        try:
            with conn.cursor(pymysql.cursors.DictCursor) as cursor:
                query = """
                    SELECT id, tanggal, harga_usd, harga_idr
                    FROM tb_harga_emas
                    WHERE tanggal = %s
                """
                cursor.execute(query, (date_str,))
                result = cursor.fetchone()
                return result
        except Exception as e:
            print(f"[ERROR] Error cek data existing: {e}")
            return None

    def save_to_database(self, data):
        """Menyimpan data harga ke database"""
        print(f"\n[INFO] Menyimpan data ke database...")

        try:
            conn = self.get_db_connection()
        except Exception as e:
            print(f"[ERROR] Gagal mendapatkan koneksi database: {e}")
            return False

        previous_price = None
        saved_count = 0
        updated_count = 0
        error_count = 0

        try:
            with conn.cursor() as cursor:
                for date, row in data.iterrows():
                    tanggal = date.strftime('%Y-%m-%d')
                    harga_usd = round(row['Close'], 2)
                    harga_idr = self.convert_usd_to_idr_per_gram(harga_usd)

                    # Hitung perubahan persen
                    if previous_price:
                        perubahan = self.calculate_percentage_change(harga_usd, previous_price)
                    else:
                        perubahan = 0

                    query = """
                        INSERT INTO tb_harga_emas
                        (tanggal, harga_usd, harga_idr, perubahan_persen, created_at, updated_at)
                        VALUES (%s, %s, %s, %s, NOW(), NOW())
                        ON DUPLICATE KEY UPDATE
                        harga_usd = VALUES(harga_usd),
                        harga_idr = VALUES(harga_idr),
                        perubahan_persen = VALUES(perubahan_persen),
                        updated_at = NOW()
                    """

                    try:
                        # Cek apakah data sudah ada
                        existing = self.check_existing_data(conn, tanggal)

                        cursor.execute(query, (tanggal, harga_usd, harga_idr, perubahan))

                        if existing:
                            updated_count += 1
                        else:
                            saved_count += 1

                        previous_price = harga_usd

                        # Progress indicator setiap 50 record
                        total_processed = saved_count + updated_count
                        if total_processed % 50 == 0:
                            print(f"[PROGRESS] {total_processed} record diproses...")
                            conn.commit()

                    except pymysql.MySQLError as err:
                        error_count += 1
                        print(f"[ERROR] Error pada tanggal {tanggal}: {err}")

            # Commit terakhir
            conn.commit()

            print(f"\n[SUCCESS] Proses selesai:")
            print(f"[SUCCESS] Data baru disimpan: {saved_count}")
            print(f"[SUCCESS] Data diupdate: {updated_count}")
            print(f"[SUCCESS] Total diproses: {saved_count + updated_count}")
            if error_count > 0:
                print(f"[WARNING] Error: {error_count}")

            return True

        except pymysql.MySQLError as err:
            print(f"\n[ERROR] Error menyimpan data: {err}")
            conn.rollback()
            return False
        except Exception as e:
            print(f"\n[ERROR] Error tidak terduga: {e}")
            import traceback
            traceback.print_exc()
            conn.rollback()
            return False
        finally:
            conn.close()

    def update_historical(self, years=5):
        """Update data historis"""
        print("\n" + "="*70)
        print(f"UPDATE DATA HISTORIS ({years} TAHUN)")
        print("="*70)

        start_date = (datetime.now() - timedelta(days=365*years)).strftime('%Y-%m-%d')
        end_date = datetime.now().strftime('%Y-%m-%d')

        print(f"\n[INFO] Periode: {start_date} sampai {end_date}")

        data = self.fetch_gold_price(start_date=start_date, end_date=end_date)

        if data is not None and not data.empty:
            success = self.save_to_database(data)

            if success:
                print("\n" + "="*70)
                print("[SUCCESS] UPDATE HISTORIS SELESAI!")
                print("="*70)
                return True
            else:
                print("\n" + "="*70)
                print("[ERROR] UPDATE HISTORIS GAGAL!")
                print("="*70)
                return False
        else:
            print(f"\n[ERROR] Update gagal - tidak ada data")
            return False

    def show_statistics(self):
        """Menampilkan statistik database"""
        print("\n" + "="*70)
        print("STATISTIK DATABASE")
        print("="*70)

        try:
            conn = self.get_db_connection()
            with conn.cursor(pymysql.cursors.DictCursor) as cursor:
                # Total records
                cursor.execute("SELECT COUNT(*) as total FROM tb_harga_emas")
                total = cursor.fetchone()['total']

                # Data terakhir
                cursor.execute("""
                    SELECT tanggal, harga_usd, harga_idr
                    FROM tb_harga_emas
                    ORDER BY tanggal DESC
                    LIMIT 1
                """)
                latest = cursor.fetchone()

                # Data tertua
                cursor.execute("""
                    SELECT tanggal, harga_usd, harga_idr
                    FROM tb_harga_emas
                    ORDER BY tanggal ASC
                    LIMIT 1
                """)
                oldest = cursor.fetchone()

                # Harga tertinggi
                cursor.execute("""
                    SELECT tanggal, harga_usd, harga_idr
                    FROM tb_harga_emas
                    ORDER BY harga_usd DESC
                    LIMIT 1
                """)
                highest = cursor.fetchone()

                # Harga terendah
                cursor.execute("""
                    SELECT tanggal, harga_usd, harga_idr
                    FROM tb_harga_emas
                    ORDER BY harga_usd ASC
                    LIMIT 1
                """)
                lowest = cursor.fetchone()

                print(f"\n[INFO] Total Data: {total:,} record")

                if latest:
                    print(f"\n[INFO] Data Terbaru:")
                    print(f"[INFO] Tanggal: {latest['tanggal']}")
                    print(f"[INFO] Harga: ${latest['harga_usd']:.2f} / Rp {latest['harga_idr']:,.0f}")

                if oldest:
                    print(f"\n[INFO] Data Tertua:")
                    print(f"[INFO] Tanggal: {oldest['tanggal']}")
                    print(f"[INFO] Harga: ${oldest['harga_usd']:.2f} / Rp {oldest['harga_idr']:,.0f}")

                if highest:
                    print(f"\n[INFO] Harga Tertinggi:")
                    print(f"[INFO] Tanggal: {highest['tanggal']}")
                    print(f"[INFO] Harga: ${highest['harga_usd']:.2f} / Rp {highest['harga_idr']:,.0f}")

                if lowest:
                    print(f"\n[INFO] Harga Terendah:")
                    print(f"[INFO] Tanggal: {lowest['tanggal']}")
                    print(f"[INFO] Harga: ${lowest['harga_usd']:.2f} / Rp {lowest['harga_idr']:,.0f}")

            conn.close()
            print("\n" + "="*70)

        except Exception as e:
            print(f"\n[ERROR] Error mengambil statistik: {e}")

def print_help():
    """Menampilkan bantuan penggunaan"""
    print("\n" + "="*70)
    print("CARA PENGGUNAAN")
    print("="*70)
    print("\nPerintah yang tersedia:")
    print("python update_data.py historical <years>  # Update data historis")
    print("python update_data.py stats                # Tampilkan statistik")
    print("python update_data.py help                 # Tampilkan bantuan ini")
    print("\nContoh:")
    print("python update_data.py historical 1")
    print("python update_data.py historical 5")
    print("python update_data.py historical 10")
    print("="*70 + "\n")

def main():
    print("\n" + "="*70)
    print("GOLD DATA UPDATER v2.0")
    print("="*70)
    print("Update harga emas dari Yahoo Finance ke Database")
    print("="*70)

    updater = GoldDataUpdater()

    if len(sys.argv) > 1:
        command = sys.argv[1].lower()

        if command == 'historical':
            years = int(sys.argv[2]) if len(sys.argv) > 2 else 5
            print(f"\n[INFO] Memulai update data {years} tahun...")
            updater.update_historical(years)
        elif command == 'stats':
            updater.show_statistics()
        elif command == 'help':
            print_help()
        else:
            print(f"\n[ERROR] Perintah tidak dikenal: {sys.argv[1]}")
            print_help()
    else:
        # Default: tampilkan help
        print_help()

if __name__ == "__main__":
    main()

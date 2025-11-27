# -*- coding: utf-8 -*-
import yfinance as yf
import pymysql
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv
import sys
import io
import json
import logging

# Set UTF-8 encoding untuk Windows
if sys.platform == 'win32':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')

# Load environment variables
load_dotenv()

# Setup file logging
log_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'logs')
if not os.path.exists(log_dir):
    os.makedirs(log_dir)

log_file = os.path.join(log_dir, f'update_data_{datetime.now().strftime("%Y%m%d")}.log')

logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler(log_file, encoding='utf-8'),
        logging.StreamHandler(sys.stdout)
    ]
)

logger = logging.getLogger(__name__)

class GoldDataUpdater:
    def __init__(self):
        logger.info("=" * 70)
        logger.info("Initializing GoldDataUpdater")
        logger.info(f"Python Version: {sys.version}")
        logger.info(f"Working Directory: {os.getcwd()}")
        logger.info(f"Script Directory: {os.path.dirname(os.path.abspath(__file__))}")
        logger.info(f"Log File: {log_file}")

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
        self.kurs_usd_to_idr = 16718

        print("[DEBUG] Database Config:")
        logger.debug("Database Configuration:")
        logger.debug(f"Host: {self.db_config['host']}")
        logger.debug(f"Port: {self.db_config['port']}")
        logger.debug(f"Database: {self.db_config['database']}")
        logger.debug(f"User: {self.db_config['user']}")
        logger.debug(f"Password: {'(empty)' if not self.db_config['password'] else '***'}")

        print(f"[DEBUG] Host: {self.db_config['host']}")
        print(f"[DEBUG] Port: {self.db_config['port']}")
        print(f"[DEBUG] Database: {self.db_config['database']}")
        print(f"[DEBUG] User: {self.db_config['user']}")
        print(f"[DEBUG] Password: {'(empty)' if not self.db_config['password'] else '***'}")

    def get_db_connection(self):
        """Membuat koneksi ke database"""
        try:
            print("[INFO] Mencoba koneksi ke database...")
            logger.info("Attempting database connection...")

            conn = pymysql.connect(**self.db_config)

            # Test koneksi
            with conn.cursor() as cursor:
                cursor.execute("SELECT 1")
                cursor.fetchone()

                # Test akses ke tabel
                cursor.execute("SELECT COUNT(*) as total FROM tb_harga_emas")
                result = cursor.fetchone()
                msg = f"Koneksi database berhasil - Total data existing: {result[0]}"
                print(f"[SUCCESS] {msg}")
                logger.info(msg)

            return conn

        except pymysql.MySQLError as err:
            error_msg = f"Error koneksi database MySQL: {err} (Error Code: {err.args[0]})"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            logger.exception("Full MySQL Error:")
            sys.exit(1)

        except Exception as e:
            error_msg = f"Error tidak terduga: {e}"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            logger.exception("Full Exception Traceback:")
            sys.exit(1)

    def fetch_gold_price(self, start_date=None, end_date=None):
        """Mengambil data harga emas dari Yahoo Finance"""
        try:
            print("[INFO] Mengambil data dari Yahoo Finance...")
            logger.info("Fetching gold price data from Yahoo Finance...")

            gold_ticker = yf.Ticker("GC=F")

            if not start_date:
                start_date = (datetime.now() - timedelta(days=365*5)).strftime('%Y-%m-%d')

            if not end_date:
                end_date = datetime.now().strftime('%Y-%m-%d')

            msg = f"Periode: {start_date} sampai {end_date}"
            print(f"[INFO] {msg}")
            logger.info(msg)

            hist = gold_ticker.history(start=start_date, end=end_date)

            if hist.empty:
                error_msg = "Tidak ada data yang ditemukan dari Yahoo Finance"
                print(f"[ERROR] {error_msg}")
                logger.error(error_msg)
                return None

            msg = f"Berhasil mengambil {len(hist)} baris data"
            print(f"[SUCCESS] {msg}")
            logger.info(msg)

            msg = f"Rentang: {hist.index[0].strftime('%Y-%m-%d')} s/d {hist.index[-1].strftime('%Y-%m-%d')}"
            print(f"[INFO] {msg}")
            logger.info(msg)

            msg = f"Harga terakhir: ${hist['Close'].iloc[-1]:.2f} per troy ounce"
            print(f"[INFO] {msg}")
            logger.info(msg)

            # Debug: tampilkan 3 data terakhir
            print("\n[DEBUG] 3 Data Terakhir dari Yahoo Finance:")
            logger.debug("Last 3 data points from Yahoo Finance:")
            for i in range(min(3, len(hist))):
                idx = -(i+1)
                date = hist.index[idx]
                price = hist['Close'].iloc[idx]
                msg = f"{date.strftime('%Y-%m-%d')}: ${price:.2f}"
                print(f"[DEBUG] {msg}")
                logger.debug(msg)
            print()

            return hist

        except Exception as e:
            error_msg = f"Error mengambil data: {e}"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            logger.exception("Full Exception Traceback:")
            return None

    def calculate_percentage_change(self, current_price, previous_price):
        """Menghitung persentase perubahan harga"""
        if previous_price == 0:
            return 0
        return ((current_price - previous_price) / previous_price) * 100

    def convert_usd_to_idr_per_gram(self, price_usd_per_oz):
        """Konversi harga dari USD/oz ke IDR/gram"""
        price_usd_per_gram = price_usd_per_oz / 31.1
        price_idr_per_gram = price_usd_per_gram * self.kurs_usd_to_idr
        return round(price_idr_per_gram, 2)

    def check_existing_data(self, conn, date_str):
        """Cek apakah data sudah ada di database"""
        try:
            with conn.cursor(pymysql.cursors.DictCursor) as cursor:
                query = "SELECT id, tanggal, harga_usd, harga_idr FROM tb_harga_emas WHERE tanggal = %s"
                cursor.execute(query, (date_str,))
                result = cursor.fetchone()
                logger.debug(f"Check existing data for {date_str}: {'Found' if result else 'Not found'}")
                return result
        except Exception as e:
            error_msg = f"Error cek data existing: {e}"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            return None

    def save_to_database(self, data):
        """Menyimpan data harga ke database"""
        print("[INFO] Menyimpan data ke database...")
        logger.info("Starting database save operation...")

        try:
            conn = self.get_db_connection()
        except Exception as e:
            error_msg = f"Gagal mendapatkan koneksi database: {e}"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            return False

        previous_price = None
        saved_count = 0
        updated_count = 0
        error_count = 0
        skipped_count = 0

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

                    try:
                        # Cek apakah data sudah ada
                        existing = self.check_existing_data(conn, tanggal)

                        if existing:
                            # Cek apakah harga berbeda
                            if abs(float(existing['harga_usd']) - harga_usd) < 0.01:
                                skipped_count += 1
                                msg = f"{tanggal}: Data sama, skip"
                                print(f"[SKIP] {msg}")
                                logger.debug(f"Skipped: {msg}")
                                previous_price = harga_usd
                                continue
                            else:
                                msg = f"{tanggal}: ${existing['harga_usd']:.2f} -> ${harga_usd:.2f}"
                                print(f"[UPDATE] {msg}")
                                logger.info(f"Updating: {msg}")

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

                        logger.debug(f"Executing query for {tanggal}: USD ${harga_usd}, IDR Rp{harga_idr}")
                        cursor.execute(query, (tanggal, harga_usd, harga_idr, perubahan))

                        if existing:
                            updated_count += 1
                            msg = f"Updated: {tanggal}"
                            print(f"[SUCCESS] {msg}")
                            logger.info(msg)
                        else:
                            saved_count += 1
                            msg = f"Inserted: {tanggal}"
                            print(f"[SUCCESS] {msg}")
                            logger.info(msg)

                        previous_price = harga_usd

                    except pymysql.MySQLError as err:
                        error_count += 1
                        error_msg = f"Error pada tanggal {tanggal}: {err}"
                        print(f"[ERROR] {error_msg}")
                        logger.error(error_msg)

            # COMMIT semua perubahan
            conn.commit()
            print("\n[SUCCESS] Database COMMIT berhasil!")
            logger.info("Database COMMIT successful!")

            summary = f"Saved: {saved_count}, Updated: {updated_count}, Skipped: {skipped_count}, Errors: {error_count}"
            print("\n[SUMMARY] Proses selesai:")
            print(f"[SUMMARY] Data baru disimpan: {saved_count}")
            print(f"[SUMMARY] Data diupdate: {updated_count}")
            print(f"[SUMMARY] Data di-skip (sama): {skipped_count}")
            if error_count > 0:
                print(f"[SUMMARY] Error: {error_count}")

            logger.info(f"Summary: {summary}")

            # Output JSON untuk Laravel
            result = {
                'success': True,
                'saved': saved_count,
                'updated': updated_count,
                'skipped': skipped_count,
                'errors': error_count
            }
            json_output = json.dumps(result)
            print(f"\n[JSON_RESULT]{json_output}[/JSON_RESULT]")
            logger.info(f"JSON Result: {json_output}")

            return True

        except Exception as e:
            error_msg = f"Error menyimpan data: {e}"
            print(f"\n[ERROR] {error_msg}")
            logger.error(error_msg)
            logger.exception("Full Exception Traceback:")
            conn.rollback()
            logger.warning("Database ROLLBACK executed")
            return False
        finally:
            conn.close()
            print("[INFO] Database connection closed")
            logger.info("Database connection closed")

    def get_latest_database_date(self):
        """Mendapatkan tanggal data terakhir di database"""
        try:
            conn = self.get_db_connection()
            with conn.cursor(pymysql.cursors.DictCursor) as cursor:
                query = """
                    SELECT tanggal, harga_usd, harga_idr
                    FROM tb_harga_emas
                    ORDER BY tanggal DESC
                    LIMIT 1
                """
                cursor.execute(query)
                result = cursor.fetchone()
                conn.close()

                if result:
                    logger.info(f"Latest DB data: {result['tanggal']} - ${result['harga_usd']}")

                return result
        except Exception as e:
            error_msg = f"Error mengambil data terakhir: {e}"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            return None

    def update_today(self):
        """Update harga emas hari ini"""
        print("=" * 70)
        print("[INFO] GOLD DATA UPDATER v2.1")
        print("=" * 70)
        print("[INFO] Update harga emas dari Yahoo Finance ke Database")
        print("=" * 70)

        logger.info("=" * 70)
        logger.info("GOLD DATA UPDATER v2.1 - Update Started")
        logger.info("=" * 70)

        today = datetime.now()
        msg = f"Tanggal sistem: {today.strftime('%Y-%m-%d %H:%M:%S')}"
        print(f"[INFO] {msg}")
        logger.info(msg)

        # Cek data terakhir di database
        latest_db = self.get_latest_database_date()
        if latest_db:
            print(f"[INFO] Data terakhir di database:")
            print(f"[INFO] Tanggal: {latest_db['tanggal']}")
            print(f"[INFO] Harga: ${latest_db['harga_usd']:.2f}")

        # Ambil data 10 hari terakhir
        start_date = (today - timedelta(days=10)).strftime('%Y-%m-%d')
        end_date = today.strftime('%Y-%m-%d')

        data = self.fetch_gold_price(start_date=start_date, end_date=end_date)

        if data is not None and not data.empty:
            success = self.save_to_database(data)

            if success:
                print("\n" + "=" * 70)
                print("[SUCCESS] UPDATE SELESAI!")
                print("=" * 70)
                logger.info("Update completed successfully!")
                logger.info("=" * 70)
                return True
            else:
                print("\n" + "=" * 70)
                print("[ERROR] UPDATE GAGAL!")
                print("=" * 70)
                logger.error("Update failed!")
                logger.error("=" * 70)
                return False
        else:
            error_msg = "Tidak ada data dari Yahoo Finance"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
            return False

def main():
    logger.info("Script execution started")
    logger.info(f"Command line arguments: {sys.argv}")

    updater = GoldDataUpdater()

    if len(sys.argv) > 1:
        command = sys.argv[1].lower()
        logger.info(f"Command received: {command}")
        if command == 'today':
            updater.update_today()
        else:
            error_msg = f"Unknown command: {command}"
            print(f"[ERROR] {error_msg}")
            logger.error(error_msg)
    else:
        logger.info("No command specified, running default: today")
        updater.update_today()

    logger.info("Script execution finished")

if __name__ == "__main__":
    main()

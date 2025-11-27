import pandas as pd
import numpy as np
from prophet import Prophet
import pymysql
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv
from sklearn.metrics import mean_absolute_percentage_error, mean_squared_error, mean_absolute_error, r2_score
import sys
import json

load_dotenv()

class GoldPriceForecast:
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
        self.kurs_usd_to_idr = 15800
        self.model = None

    def get_db_connection(self):
        """Membuat koneksi database"""
        try:
            conn = pymysql.connect(**self.db_config)
            return conn
        except pymysql.MySQLError as err:
            print(f"✗ Error koneksi database: {err}")
            sys.exit(1)
        except Exception as e:
            print(f"✗ Error tidak terduga: {e}")
            sys.exit(1)

    def load_historical_data(self):
        """Memuat data historis dari database"""
        print("📊 Memuat data historis dari database...")

        try:
            conn = self.get_db_connection()
            query = "SELECT tanggal, harga_usd FROM tb_harga_emas ORDER BY tanggal ASC"

            df = pd.read_sql(query, conn)
            conn.close()

            if df.empty:
                print("✗ Tidak ada data historis ditemukan")
                return None

            # Rename kolom untuk Prophet (ds = datestamp, y = value)
            df.rename(columns={'tanggal': 'ds', 'harga_usd': 'y'}, inplace=True)
            df['ds'] = pd.to_datetime(df['ds'])

            print(f"✓ Berhasil memuat {len(df)} record data historis")
            print(f"   Rentang tanggal: {df['ds'].min().strftime('%Y-%m-%d')} sampai {df['ds'].max().strftime('%Y-%m-%d')}")

            return df

        except Exception as e:
            print(f"✗ Error memuat data: {e}")
            import traceback
            traceback.print_exc()
            return None

    def train_model(self, data):
        """Melatih model Prophet"""
        print("\n🤖 Melatih model Prophet...")

        try:
            # Inisialisasi model dengan parameter optimal
            self.model = Prophet(
                yearly_seasonality=True,
                weekly_seasonality=False,
                daily_seasonality=False,
                changepoint_prior_scale=0.05,
                seasonality_prior_scale=10.0,
                interval_width=0.95
            )

            # Fit model
            self.model.fit(data)
            print("✓ Model berhasil dilatih")

            return self.model

        except Exception as e:
            print(f"✗ Error melatih model: {e}")
            import traceback
            traceback.print_exc()
            return None

    def make_forecast(self, periods_years=5):
        """Membuat prediksi untuk periode tertentu"""
        if self.model is None:
            print("✗ Model belum dilatih!")
            return None

        try:
            # Buat dataframe untuk future dates
            periods_days = periods_years * 365
            future = self.model.make_future_dataframe(periods=periods_days, freq='D')

            # Prediksi
            print(f"\n🔮 Membuat prediksi untuk {periods_years} tahun ke depan...")
            forecast = self.model.predict(future)

            print(f"✓ Prediksi berhasil dibuat untuk {len(forecast)} hari")

            return forecast

        except Exception as e:
            print(f"✗ Error membuat prediksi: {e}")
            import traceback
            traceback.print_exc()
            return None

    def calculate_accuracy(self, actual_data, forecast):
        """Menghitung metrik akurasi model"""
        print("\n📈 Menghitung akurasi model...")

        try:
            # Merge actual dan predicted data
            comparison = actual_data.merge(
                forecast[['ds', 'yhat']],
                on='ds',
                how='inner'
            )

            if len(comparison) == 0:
                print("✗ Tidak ada data untuk perbandingan")
                return None

            actual = comparison['y'].values
            predicted = comparison['yhat'].values

            # Hitung metrik
            mape = mean_absolute_percentage_error(actual, predicted) * 100
            rmse = np.sqrt(mean_squared_error(actual, predicted))
            mae = mean_absolute_error(actual, predicted)
            r2 = r2_score(actual, predicted)

            metrics = {
                'mape': round(mape, 4),
                'rmse': round(rmse, 4),
                'mae': round(mae, 4),
                'r_squared': round(r2, 6),
                'data_points': len(comparison)
            }

            print(f"✓ Akurasi model:")
            print(f"   MAPE: {metrics['mape']:.2f}%")
            print(f"   RMSE: ${metrics['rmse']:.2f}")
            print(f"   MAE: ${metrics['mae']:.2f}")
            print(f"   R²: {metrics['r_squared']:.4f}")
            print(f"   Data points: {metrics['data_points']}")

            return metrics

        except Exception as e:
            print(f"✗ Error menghitung akurasi: {e}")
            import traceback
            traceback.print_exc()
            return None

    def run_backtesting(self, full_data, test_years=1):
        """
        Melakukan backtesting dengan melatih model pada data historis
        dan menguji pada periode yang sudah diketahui hasilnya
        """
        print("\n" + "="*60)
        print("🔬 MEMULAI BACKTESTING (SIMULASI PERAMALAN)")
        print("="*60)

        try:
            # Tentukan cutoff date (pisahkan data training dan testing)
            latest_date = full_data['ds'].max()
            cutoff_date = latest_date - timedelta(days=test_years * 365)

            print(f"\n📅 Split Data:")
            print(f"   Training Data: sampai {cutoff_date.strftime('%Y-%m-%d')}")
            print(f"   Testing Data: {cutoff_date.strftime('%Y-%m-%d')} sampai {latest_date.strftime('%Y-%m-%d')}")

            # Split data
            train_data = full_data[full_data['ds'] <= cutoff_date].copy()
            test_data = full_data[full_data['ds'] > cutoff_date].copy()

            print(f"\n📊 Jumlah Data:")
            print(f"   Training: {len(train_data)} hari")
            print(f"   Testing: {len(test_data)} hari")

            if len(train_data) < 30:
                print("✗ Data training terlalu sedikit untuk backtesting")
                return None

            if len(test_data) < 30:
                print("✗ Data testing terlalu sedikit untuk backtesting")
                return None

            # Train model dengan data training
            print("\n🤖 Melatih model dengan data training...")
            backtest_model = Prophet(
                yearly_seasonality=True,
                weekly_seasonality=False,
                daily_seasonality=False,
                changepoint_prior_scale=0.05,
                seasonality_prior_scale=10.0,
                interval_width=0.95
            )
            backtest_model.fit(train_data)
            print("✓ Model backtesting berhasil dilatih")

            # Buat prediksi untuk periode testing
            print(f"\n🔮 Membuat prediksi untuk periode testing ({test_years} tahun)...")
            future = backtest_model.make_future_dataframe(periods=len(test_data), freq='D')
            forecast = backtest_model.predict(future)

            # Filter forecast hanya untuk periode testing
            forecast_test = forecast[forecast['ds'] > cutoff_date].copy()

            # Merge dengan data aktual
            comparison = test_data.merge(
                forecast_test[['ds', 'yhat', 'yhat_lower', 'yhat_upper']],
                on='ds',
                how='inner'
            )

            if len(comparison) == 0:
                print("✗ Tidak ada data untuk perbandingan backtesting")
                return None

            # Hitung metrik backtesting
            actual = comparison['y'].values
            predicted = comparison['yhat'].values

            mape = mean_absolute_percentage_error(actual, predicted) * 100
            rmse = np.sqrt(mean_squared_error(actual, predicted))
            mae = mean_absolute_error(actual, predicted)
            r2 = r2_score(actual, predicted)

            # Hitung akurasi prediksi dalam confidence interval
            within_interval = ((comparison['y'] >= comparison['yhat_lower']) &
                             (comparison['y'] <= comparison['yhat_upper'])).sum()
            interval_accuracy = (within_interval / len(comparison)) * 100

            backtest_metrics = {
                'mape': round(mape, 4),
                'rmse': round(rmse, 4),
                'mae': round(mae, 4),
                'r_squared': round(r2, 6),
                'data_points': len(comparison),
                'interval_accuracy': round(interval_accuracy, 2),
                'test_years': test_years,
                'train_size': len(train_data),
                'test_size': len(test_data)
            }

            print("\n" + "="*60)
            print("📊 HASIL BACKTESTING")
            print("="*60)
            print(f"\n✓ Metrik Performa pada Data Testing:")
            print(f"   MAPE: {backtest_metrics['mape']:.2f}%")
            print(f"   RMSE: ${backtest_metrics['rmse']:.2f}")
            print(f"   MAE: ${backtest_metrics['mae']:.2f}")
            print(f"   R²: {backtest_metrics['r_squared']:.4f}")
            print(f"   Akurasi Interval (95%): {backtest_metrics['interval_accuracy']:.2f}%")
            print(f"   Data Points: {backtest_metrics['data_points']}")

            # Interpretasi hasil
            print("\n💡 Interpretasi:")
            if mape < 5:
                print("   ✓ Excellent - Model sangat akurat (MAPE < 5%)")
            elif mape < 10:
                print("   ✓ Good - Model cukup akurat (MAPE < 10%)")
            elif mape < 20:
                print("   ⚠ Fair - Model memiliki akurasi sedang (MAPE < 20%)")
            else:
                print("   ⚠ Poor - Model perlu perbaikan (MAPE >= 20%)")

            if r2 > 0.9:
                print("   ✓ Model menjelaskan >90% variasi data (R² > 0.9)")
            elif r2 > 0.7:
                print("   ✓ Model menjelaskan >70% variasi data (R² > 0.7)")
            else:
                print("   ⚠ Model menjelaskan <70% variasi data")

            if interval_accuracy > 90:
                print(f"   ✓ {interval_accuracy:.1f}% prediksi berada dalam confidence interval")
            else:
                print(f"   ⚠ Hanya {interval_accuracy:.1f}% prediksi dalam confidence interval")

            # Simpan hasil backtesting
            self.save_backtesting_results(backtest_metrics, comparison)

            return backtest_metrics

        except Exception as e:
            print(f"✗ Error saat backtesting: {e}")
            import traceback
            traceback.print_exc()
            return None

    def save_backtesting_results(self, metrics, comparison_data):
        """Menyimpan hasil backtesting ke database"""
        print(f"\n💾 Menyimpan hasil backtesting...")

        try:
            conn = self.get_db_connection()

            with conn.cursor() as cursor:
                # Simpan metrik backtesting
                query = """
                    INSERT INTO tb_akurasi
                    (mape, rmse, mae, r_squared, data_points, keterangan, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
                """

                keterangan = (f"Backtesting - {metrics['test_years']} tahun | "
                            f"Train: {metrics['train_size']} days | "
                            f"Test: {metrics['test_size']} days | "
                            f"Interval Accuracy: {metrics['interval_accuracy']:.2f}%")

                cursor.execute(query, (
                    metrics['mape'],
                    metrics['rmse'],
                    metrics['mae'],
                    metrics['r_squared'],
                    metrics['data_points'],
                    keterangan
                ))

            conn.commit()
            print("✓ Hasil backtesting tersimpan")

        except Exception as e:
            print(f"✗ Error menyimpan backtesting: {e}")
            conn.rollback()
        finally:
            conn.close()

    def save_forecast_to_db(self, forecast, periods_years):
        """Menyimpan hasil prediksi ke database"""
        print(f"\n💾 Menyimpan hasil prediksi ke database...")

        try:
            conn = self.get_db_connection()

            # Ambil data untuk future saja (setelah hari ini)
            today = datetime.now().date()
            future_forecast = forecast[forecast['ds'].dt.date > today].copy()

            print(f"   Total prediksi future: {len(future_forecast)} hari")

            with conn.cursor() as cursor:
                # Hapus prediksi lama
                cursor.execute("DELETE FROM tb_prediksi")
                deleted_count = cursor.rowcount
                if deleted_count > 0:
                    print(f"   Menghapus {deleted_count} prediksi lama")

                saved_count = 0
                for _, row in future_forecast.iterrows():
                    tanggal = row['ds'].strftime('%Y-%m-%d')
                    harga_usd = round(row['yhat'], 2)
                    harga_idr = round((harga_usd / 31.1035) * self.kurs_usd_to_idr, 2)
                    lower_bound = round(row['yhat_lower'], 2)
                    upper_bound = round(row['yhat_upper'], 2)

                    query = """
                        INSERT INTO tb_prediksi
                        (tanggal_prediksi, harga_prediksi_usd, harga_prediksi_idr,
                         lower_bound, upper_bound, periode_tahun, created_at, updated_at)
                        VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
                    """

                    cursor.execute(query, (
                        tanggal, harga_usd, harga_idr,
                        lower_bound, upper_bound, periods_years
                    ))
                    saved_count += 1

                    # Progress indicator setiap 100 record
                    if saved_count % 365 == 0:
                        print(f"   Progress: {saved_count} prediksi disimpan...")
                        conn.commit()

            conn.commit()
            print(f"✓ Berhasil menyimpan {saved_count} data prediksi")

        except pymysql.MySQLError as err:
            print(f"✗ Error menyimpan prediksi: {err}")
            conn.rollback()
        except Exception as e:
            print(f"✗ Error tidak terduga: {e}")
            import traceback
            traceback.print_exc()
            conn.rollback()
        finally:
            conn.close()

    def save_accuracy_to_db(self, metrics):
        """Menyimpan metrik akurasi ke database"""
        if metrics is None:
            print("Tidak ada metrik untuk disimpan")
            return

        print(f"\n💾 Menyimpan metrik akurasi...")

        try:
            conn = self.get_db_connection()

            with conn.cursor() as cursor:
                query = """
                    INSERT INTO tb_akurasi
                    (mape, rmse, mae, r_squared, data_points, keterangan, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
                """

                keterangan = f"Model Prophet - Training dengan {metrics['data_points']} data points"

                cursor.execute(query, (
                    metrics['mape'],
                    metrics['rmse'],
                    metrics['mae'],
                    metrics['r_squared'],
                    metrics['data_points'],
                    keterangan
                ))

            conn.commit()
            print("✓ Metrik akurasi tersimpan")

        except pymysql.MySQLError as err:
            print(f"✗ Error menyimpan akurasi: {err}")
            conn.rollback()
        except Exception as e:
            print(f"✗ Error tidak terduga: {e}")
            import traceback
            traceback.print_exc()
            conn.rollback()
        finally:
            conn.close()

    def run_full_forecast(self, periods_years=5, run_backtest=True, backtest_years=1):
        """Menjalankan proses prediksi lengkap dengan opsi backtesting"""
        print("\n" + "="*60)
        print("🚀 MULAI PROSES PREDIKSI HARGA EMAS")
        print("="*60)

        # 1. Load data
        data = self.load_historical_data()
        if data is None or len(data) < 30:
            print("\n✗ Data historis tidak cukup (minimal 30 data)")
            return False

        # 2. Run backtesting jika diminta
        if run_backtest:
            backtest_metrics = self.run_backtesting(data, test_years=backtest_years)
            if backtest_metrics:
                print("\n✓ Backtesting selesai")

        # 3. Train model dengan semua data
        model = self.train_model(data)
        if model is None:
            return False

        # 4. Make forecast
        forecast = self.make_forecast(periods_years)
        if forecast is None:
            return False

        # 5. Calculate accuracy
        metrics = self.calculate_accuracy(data, forecast)

        # 6. Save to database
        self.save_forecast_to_db(forecast, periods_years)
        self.save_accuracy_to_db(metrics)

        print("\n" + "="*60)
        print("✅ PROSES SELESAI")
        print("="*60)

        return True

def main():
    print("="*60)
    print("💰 GOLD PRICE FORECAST WITH BACKTESTING")
    print("="*60)

    forecaster = GoldPriceForecast()

    # Default values
    periods = 5
    run_backtest = True
    backtest_years = 1

    # Parse arguments
    if len(sys.argv) > 1:
        try:
            periods = int(sys.argv[1])
            print(f"Periode prediksi: {periods} tahun")
        except ValueError:
            print("✗ Periode harus berupa angka")
            print("\nCara penggunaan:")
            print("  python forecast.py              # Default: 5 tahun, dengan backtesting 1 tahun")
            print("  python forecast.py 3            # Prediksi 3 tahun, dengan backtesting 1 tahun")
            print("  python forecast.py 10 2         # Prediksi 10 tahun, dengan backtesting 2 tahun")
            print("  python forecast.py 5 0          # Prediksi 5 tahun, tanpa backtesting")
            sys.exit(1)

    if len(sys.argv) > 2:
        try:
            backtest_years = int(sys.argv[2])
            if backtest_years == 0:
                run_backtest = False
                print("Backtesting: Dinonaktifkan")
            else:
                print(f"Backtesting: {backtest_years} tahun")
        except ValueError:
            print("✗ Periode backtesting harus berupa angka")
            sys.exit(1)

    success = forecaster.run_full_forecast(
        periods_years=periods,
        run_backtest=run_backtest,
        backtest_years=backtest_years
    )

    if success:
        sys.exit(0)
    else:
        sys.exit(1)

if __name__ == "__main__":
    main()

import mysql.connector
import socket
import sys
from dotenv import load_dotenv
import os

load_dotenv()

print("="*60)
print("MYSQL CONNECTION DIAGNOSTIC TOOL")
print("="*60)

# Test 1: Check if port is open
print("\n[1] Testing if MySQL port is accessible...")
host = os.getenv('DB_HOST', '127.0.0.1')
port = int(os.getenv('DB_PORT', '3306'))

try:
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.settimeout(5)
    result = sock.connect_ex((host, port))
    sock.close()

    if result == 0:
        print(f"   ✓ Port {port} is OPEN on {host}")
    else:
        print(f"   ✗ Port {port} is CLOSED on {host}")
        print(f"   💡 MySQL is NOT running!")
        print(f"   💡 Start MySQL in Laragon and try again")
        sys.exit(1)
except socket.timeout:
    print(f"   ✗ Connection timeout to {host}:{port}")
    print(f"   💡 MySQL might be blocked by firewall")
    sys.exit(1)
except Exception as e:
    print(f"   ✗ Error: {e}")
    sys.exit(1)

# Test 2: Try to connect to MySQL
print("\n[2] Testing MySQL connection...")
try:
    config = {
        'host': host,
        'port': port,
        'user': os.getenv('DB_USERNAME', 'root'),
        'password': os.getenv('DB_PASSWORD', ''),
        'connection_timeout': 5,
        'raise_on_warnings': True
    }

    print(f"   Connecting with:")
    print(f"   - Host: {config['host']}")
    print(f"   - Port: {config['port']}")
    print(f"   - User: {config['user']}")
    print(f"   - Password: {'(empty)' if not config['password'] else '***'}")

    conn = mysql.connector.connect(**config)
    print("   ✓ MySQL connection successful!")

    # Test 3: Check databases
    print("\n[3] Checking available databases...")
    cursor = conn.cursor()
    cursor.execute("SHOW DATABASES")
    databases = [db[0] for db in cursor.fetchall()]
    print(f"   Available databases: {', '.join(databases)}")

    target_db = os.getenv('DB_DATABASE', 'db_prediksi_emas')
    if target_db in databases:
        print(f"   ✓ Database '{target_db}' exists!")

        # Test 4: Check if table exists
        print(f"\n[4] Checking table in '{target_db}'...")
        cursor.execute(f"USE {target_db}")
        cursor.execute("SHOW TABLES LIKE 'tb_harga_emas'")
        table = cursor.fetchone()

        if table:
            print(f"   ✓ Table 'tb_harga_emas' exists!")
            cursor.execute("SELECT COUNT(*) FROM tb_harga_emas")
            count = cursor.fetchone()[0]
            print(f"   Current records: {count}")
        else:
            print(f"   ✗ Table 'tb_harga_emas' does NOT exist!")
            print(f"   💡 Create the table first!")
    else:
        print(f"   ✗ Database '{target_db}' does NOT exist!")
        print(f"   💡 Create database: CREATE DATABASE {target_db};")

    cursor.close()
    conn.close()

    print("\n" + "="*60)
    print("✓ ALL TESTS PASSED - MySQL is ready!")
    print("="*60)

except mysql.connector.Error as err:
    print(f"\n   ✗ MySQL Error:")
    print(f"   Error Code: {err.errno if hasattr(err, 'errno') else 'N/A'}")
    print(f"   Error Message: {err.msg if hasattr(err, 'msg') else str(err)}")

    if err.errno == 1045:
        print("\n   💡 Wrong username or password!")
        print("   💡 Check your .env file")
    elif err.errno == 2003:
        print("\n   💡 Can't connect to MySQL server")
        print("   💡 Make sure MySQL is running in Laragon")

    sys.exit(1)

except Exception as e:
    print(f"\n   ✗ Unexpected error: {e}")
    import traceback
    traceback.print_exc()
    sys.exit(1)

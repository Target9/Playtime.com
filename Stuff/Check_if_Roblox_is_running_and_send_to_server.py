import psutil
import requests
from http.server import BaseHTTPRequestHandler, HTTPServer
import threading
import time
import signal
import sys

SERVER_ENDPOINT = "http://192.168.87.2/server_side/track_roblox.php"
PERSON_NAME = "Test"
GAME_NAME = "Roblox"
PROCESS_NAME = "Windows10Universal.exe"

def get_roblox_info():
    for process in psutil.process_iter():
        try:
            if PROCESS_NAME in process.name():
                print(f"{GAME_NAME} is currently running.")
                return True, GAME_NAME
        except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
            pass
    print(f"{GAME_NAME} is not running.")
    return False, ""

def send_status_to_server(status, person, game_name=""):
    try:
        data = {
            "status": status,
            "person": person,
            "game": game_name
        }
        response = requests.post(SERVER_ENDPOINT, data=data)
        print(f"Data sent to server: {response.text}")
    except:
        print("Failed to send data to server.")

class KillCommandHandler(BaseHTTPRequestHandler):
    def set_default_headers(self):
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Expose-Headers', 'Roblox-Status')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Origin, Accept, Content-Type, X-Requested-With')

    def do_GET(self):
        is_running, _ = get_roblox_info()
        if self.path == '/kill':
            print("Received kill request.")
            if is_running:
                terminated = False
                for process in psutil.process_iter():
                    try:
                        if PROCESS_NAME in process.name():
                            process.terminate()
                            terminated = True
                    except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
                        pass

                if terminated:
                    print(f"{GAME_NAME} terminated successfully.")
                    self.send_response(200)
                    self.set_default_headers()
                    self.send_header("Roblox-Status", "Not Running")
                    self.end_headers()
                    self.wfile.write(b"Killed Roblox!\n")  # Added a line break after content
                else:
                    print(f"Error: Couldn't terminate {GAME_NAME}.")
                    self.send_response(500)
                    self.set_default_headers()
                    self.send_header("Roblox-Status", "Running")
                    self.end_headers()
                    self.wfile.write(b"Error: Couldn't terminate Roblox.\n")  # Added a line break after content
            else:
                print(f"{GAME_NAME} wasn't running.")
                self.send_response(200)
                self.set_default_headers()
                self.send_header("Roblox-Status", "Not Running")
                self.end_headers()
                self.wfile.write(b"Game wasn't open.\n")  # Added a line break after content

        elif self.path == '/isRunning':
            self.send_response(200)
            self.set_default_headers()
            if is_running:
                self.send_header("Roblox-Status", "Running")
                self.end_headers()
                self.wfile.write(b'Roblox is running.\n')  # Added a line break after content
            else:
                self.send_header("Roblox-Status", "Not Running")
                self.end_headers()
                self.wfile.write(b'Roblox is not running.\n')  # Added a line break after content

        else:
            self.send_response(404)
            self.set_default_headers()
            self.end_headers()

def run_server():
    server = HTTPServer(('0.0.0.0', 8000), KillCommandHandler)
    
    def stop_server(*args):
        print("Stopping server...")
        server.shutdown()
        sys.exit(0)
    
    signal.signal(signal.SIGINT, stop_server)
    
    print("HTTP server started on localhost:8000")
    server.serve_forever()

if __name__ == "__main__":
    print("Script starting...")

    def data_sending_loop():
        while True:
            is_running, game_name = get_roblox_info()
            if is_running:
                send_status_to_server("playing", PERSON_NAME, GAME_NAME)
            else:
                send_status_to_server("not_playing", PERSON_NAME)
            time.sleep(60)

    data_thread = threading.Thread(target=data_sending_loop)
    data_thread.start()

    run_server()

#RobloxPlayerBeta.exe
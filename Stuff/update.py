import shutil
import requests
import os
import sys
import subprocess
from flask import Flask
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

SCRIPT_PATH = r"C:\Users\jeghe\OneDrive\Documents\Hacking_Coding\Python\Playing_Tracker\Check_if_Roblox_is_running_and_send_to_server.py"

@app.route('/update_script')
def update_script():
    try:
        # Replace the URL below with the raw URL of your latest script version
        latest_script_url = "http://192.168.87.2/scripts/script_test.py"

        response = requests.get(latest_script_url)
        response.raise_for_status()  # Raise an exception for HTTP errors

        with open(SCRIPT_PATH, "w") as file:
            file.write(response.text)

        # Restart the script
        restart_script()

        return "Script updated and restarted successfully!", 200

    except Exception as e:
        return f"Error updating script: {e}", 500

def terminate_script():
    """Terminate the target script if it's running."""
    try:
        subprocess.Popen(["pkill", "-f", SCRIPT_PATH])
    except Exception as e:
        print(f"Error terminating script: {e}")

def restart_script():
    """Restart the Python script."""
    terminate_script()  # First, terminate the script if it's running
    subprocess.Popen([sys.executable, SCRIPT_PATH])

if __name__ == "__main__":
    restart_script()  # Start the target script when the update script is started
    app.run(host='0.0.0.0', port=5000, debug=False)

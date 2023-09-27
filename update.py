import shutil
import requests
from flask import Flask

app = Flask(__name__)

@app.route('/update_script')
def update_script():
    try:
        # Replace the URL below with the raw URL of your latest script version
        latest_script_url = "http://192.168.87.2/script.py"

        response = requests.get(latest_script_url)
        response.raise_for_status()  # Raise an exception for HTTP errors

        with open(r"C:\Users\jeghe\OneDrive\Documents\Hacking_Coding\Python\Playing_Tracker\Check_if_Roblox_is_running_and_send_to_server.py", "w") as file:

            file.write(response.text)

        # Optionally restart the server or do other post-update tasks
        return "Script updated successfully!", 200

    except Exception as e:
        return f"Error updating script: {e}", 500

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000, debug=True)

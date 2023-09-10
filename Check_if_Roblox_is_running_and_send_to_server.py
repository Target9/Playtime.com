import psutil
import requests
import time

SERVER_ENDPOINT = "http://playtime.com/server_side/track_roblox.php"
PERSON_NAME = "Test"
GAME_NAME = "Roblox"

def get_roblox_info():
    for process in psutil.process_iter():
        try:
            # If this process name doesn't work, adjust accordingly
            if "Windows10Universal.exe" in process.name():
                return True, GAME_NAME
        except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
            pass
    return False, ""


def send_status_to_server(status, person, game_name=""):
    try:
        data = {
            "status": status,
            "person": person,
            "game": game_name
        }
        requests.post(SERVER_ENDPOINT, data=data)
    except:
        print("Failed to send data to server.")

if __name__ == "__main__":
    while True:
        is_running, game_name = get_roblox_info()
        if is_running:
            send_status_to_server("playing", PERSON_NAME, GAME_NAME)
        else:
            send_status_to_server("not_playing", PERSON_NAME)
        time.sleep(5)


#The roblox process name Windows10Universal.exe or RobloxBeta.exe
#To run without a Window it's pythonw C:\Users\jeghe\OneDrive\Documents\Hacking_Coding\Python\Playing_Tracker\Check_if_Roblox_is_running_and_send_to_server.py 
#                             pythonw            Location                                                                    script name
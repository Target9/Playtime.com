import psutil

for process in psutil.process_iter():
    try:
        print(process.name())
    except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
        pass

# main.py
import os
import requests
import time

GITHUB_TOKEN = os.getenv("TOKEN")

if not TOKEN:
    raise Exception("Missing GITHUB_TOKEN environment variable")

HEADERS = {
    "Authorization": f"Bearer {GITHUB_TOKEN}",
    "Accept": "application/vnd.github+json"
}

BASE_URL = "https://api.github.com"

# Utility functions
def get_codespaces():
    url = f"{BASE_URL}/user/codespaces"
    response = requests.get(url, headers=HEADERS)
    if response.status_code == 200:
        return response.json().get("codespaces", [])
    else:
        raise Exception(f"Failed to get codespaces: {response.text}")

def wakeup_codespace(codespace):
    if codespace['state'] == 'Shutdown':
        print(f"Waking up codespace: {codespace['name']}")
        url = f"{BASE_URL}/user/codespaces/{codespace['name']}/start"
        res = requests.post(url, headers=HEADERS)
        if res.status_code == 202:
            print("Wake up initiated.")
        else:
            print(f"Failed to wake up codespace: {res.text}")
    else:
        print(f"Codespace {codespace['name']} is already running.")

def run_command(codespace_name, command="echo 'Hello from GitHub Codespace'"):
    url = f"{BASE_URL}/user/codespaces/{codespace_name}/machines/terminal"  # pseudo-endpoint
    # NOTE: Real terminal interaction is not available via API yet
    print(f"Would run command in {codespace_name}: {command}")

# Entry point
def main():
    print("Fetching Codespaces...")
    codespaces = get_codespaces()
    print(f"Found {len(codespaces)} codespaces.")

    for cs in codespaces:
        wakeup_codespace(cs)
        run_command(cs['name'])

if __name__ == "__main__":
    main()

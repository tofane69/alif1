import json
import sqlite3
import asyncio
from telethon import TelegramClient, events
from telethon.tl.types import Message

# --- Configuration ---
# Replace with your actual Telegram API credentials
API_ID = 'YOUR_API_ID'
API_HASH = 'YOUR_API_HASH'
SESSION_NAME = 'telegram_session'
DB_NAME = 'database.db'
CONFIG_FILE = 'config.json'

def load_config():
    """Loads the channel configuration from the JSON file."""
    try:
        with open(CONFIG_FILE, 'r') as f:
            return json.load()
    except FileNotFoundError:
        print(f"Error: {CONFIG_FILE} not found.")
        return None
    except json.JSONDecodeError:
        print(f"Error: Could not decode {CONFIG_FILE}.")
        return None

async def main():
    """Main function to connect to Telegram and process messages."""
    config = load_config()
    if not config:
        return

    telegram_channels = config.get('telegram_channels', [])
    if not telegram_channels:
        print("No Telegram channels found in the configuration.")
        return

    # Connect to the database
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()

    # Create a new Telegram client
    async with TelegramClient(SESSION_NAME, API_ID, API_HASH) as client:
        print("Connected to Telegram.")

        for channel_url in telegram_channels:
            try:
                channel_entity = await client.get_entity(channel_url)
                print(f"Processing channel: {channel_url}")

                async for message in client.iter_messages(channel_entity, limit=100):
                    if message.text:  # Process only text messages
                        message_unique_id = f"telegram_{message.id}"
                        link = f"https://t.me/{channel_entity.username}/{message.id}"

                        # Check if the message is already in the database
                        cursor.execute("SELECT 1 FROM articles WHERE message_unique_id = ?", (message_unique_id,))
                        if cursor.fetchone():
                            print(f"Message {message.id} already exists. Skipping.")
                            continue

                        # Insert the new message into the database
                        cursor.execute("""
                            INSERT INTO articles (source, source_channel, content, date, link, message_unique_id)
                            VALUES (?, ?, ?, ?, ?, ?)
                        """, (
                            'telegram',
                            channel_url,
                            message.text,
                            int(message.date.timestamp()),
                            link,
                            message_unique_id
                        ))
                        conn.commit()
                        print(f"Saved message {message.id} from {channel_url}")

            except Exception as e:
                print(f"Could not process channel {channel_url}. Error: {e}")

    # Close the database connection
    conn.close()
    print("Finished processing Telegram channels.")

if __name__ == '__main__':
    # This script is designed to be run with a Cron Job.
    # When you run it for the first time, Telethon will ask for your phone number,
    # a login code, and possibly a 2FA password.
    # After the first successful login, it will create a .session file
    # (e.g., telegram_session.session) to keep you logged in.
    print("Starting Telegram scraper...")
    # Using asyncio.run() to execute the async main function
    asyncio.run(main())

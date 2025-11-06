import sqlite3

# Define the name of the database file
DB_NAME = 'database.db'

def setup_database():
    """
    Sets up the database, creating the articles table and an FTS5 virtual table for searching.
    """
    try:
        # Connect to the SQLite database. If it doesn't exist, it will be created.
        conn = sqlite3.connect(DB_NAME)
        cursor = conn.cursor()

        # SQL statement to create the 'articles' table
        # This table stores the core data of messages from different sources.
        create_articles_table_sql = """
        CREATE TABLE IF NOT EXISTS articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            source TEXT NOT NULL,
            source_channel TEXT NOT NULL,
            content TEXT NOT NULL,
            date INTEGER NOT NULL,
            link TEXT,
            message_unique_id TEXT UNIQUE NOT NULL
        );
        """
        cursor.execute(create_articles_table_sql)
        print("Table 'articles' created or already exists.")

        # SQL statement to create the FTS5 virtual table
        # This table is specifically for enabling fast full-text searches on the 'content' column.
        # It mirrors the 'articles' table content.
        create_fts_table_sql = """
        CREATE VIRTUAL TABLE IF NOT EXISTS articles_fts USING fts5(
            content,
            content='articles',
            content_rowid='id'
        );
        """
        cursor.execute(create_fts_table_sql)
        print("FTS5 virtual table 'articles_fts' created or already exists.")

        # SQL trigger to keep the FTS table synchronized with the 'articles' table after inserts
        create_insert_trigger_sql = """
        CREATE TRIGGER IF NOT EXISTS articles_after_insert
        AFTER INSERT ON articles
        BEGIN
            INSERT INTO articles_fts(rowid, content) VALUES (new.id, new.content);
        END;
        """
        cursor.execute(create_insert_trigger_sql)
        print("Insert trigger for FTS created or already exists.")

        # SQL trigger to keep the FTS table synchronized with the 'articles' table after updates
        create_update_trigger_sql = """
        CREATE TRIGGER IF NOT EXISTS articles_after_update
        AFTER UPDATE ON articles
        BEGIN
            UPDATE articles_fts SET content = new.content WHERE rowid=new.id;
        END;
        """
        cursor.execute(create_update_trigger_sql)
        print("Update trigger for FTS created or already exists.")

        # SQL trigger to keep the FTS table synchronized with the 'articles' table after deletes
        create_delete_trigger_sql = """
        CREATE TRIGGER IF NOT EXISTS articles_after_delete
        AFTER DELETE ON articles
        BEGIN
            DELETE FROM articles_fts WHERE rowid=old.id;
        END;
        """
        cursor.execute(create_delete_trigger_sql)
        print("Delete trigger for FTS created or already exists.")

        # Commit the changes and close the connection
        conn.commit()
        conn.close()
        print(f"Database '{DB_NAME}' has been set up successfully.")

    except sqlite3.Error as e:
        print(f"Database error: {e}")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")

if __name__ == '__main__':
    setup_database()

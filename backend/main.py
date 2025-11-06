from fastapi import FastAPI, HTTPException, Query
from fastapi.middleware.cors import CORSMiddleware
import sqlite3
from typing import List, Dict, Any

# --- Configuration ---
DB_NAME = 'database.db'

# Create the FastAPI app instance
app = FastAPI(
    title="Content Search API",
    description="An API to search for content in Telegram and Rubika channels.",
    version="1.0.0"
)

# --- CORS Configuration ---
# Allow requests from all origins.
# This is useful for development, but for production, you might want to restrict it
# to the specific domain of your frontend.
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Allows all origins
    allow_credentials=True,
    allow_methods=["GET"], # Allows only GET methods
    allow_headers=["*"],   # Allows all headers
)

def dict_factory(cursor, row):
    """Converts database query results into a dictionary."""
    d = {}
    for idx, col in enumerate(cursor.description):
        d[col[0]] = row[idx]
    return d

@app.get("/search", response_model=List[Dict[str, Any]])
async def search_articles(q: str = Query(..., min_length=1, description="The search term to look for.")):
    """
    Searches for articles in the database that match the query.
    Results are sorted by date in descending order and limited to 20.
    """
    if not q:
        raise HTTPException(status_code=400, detail="Query parameter 'q' cannot be empty.")

    try:
        # Connect to the database
        conn = sqlite3.connect(DB_NAME)
        conn.row_factory = dict_factory  # Return rows as dictionaries
        cursor = conn.cursor()

        # Perform a search on the FTS5 virtual table
        # We join with the original 'articles' table to get all the columns we need.
        # The MATCH operator is used for full-text search.
        query = """
        SELECT
            a.source,
            a.source_channel,
            a.content,
            a.date,
            a.link
        FROM articles_fts fts
        JOIN articles a ON fts.rowid = a.id
        WHERE fts.content MATCH ?
        ORDER BY a.date DESC
        LIMIT 20;
        """
        cursor.execute(query, (q,))
        results = cursor.fetchall()

        # Close the database connection
        conn.close()

        return results

    except sqlite3.Error as e:
        # Handle potential database errors
        raise HTTPException(status_code=500, detail=f"Database error: {e}")
    except Exception as e:
        # Handle other unexpected errors
        raise HTTPException(status_code=500, detail=f"An unexpected error occurred: {e}")

# To run this server, use the command:
# uvicorn main:app --host 0.0.0.0 --port 8000

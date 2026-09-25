# D-RAG API

**Document Retrieval-Augmented Generation API**

A Laravel-based backend API that implements a document RAG pipeline using OpenAI embeddings, Pinecone vector search, MySQL, and an OpenAI LLM.

## Tech Stack

- Laravel / PHP
- MySQL
- OpenAI API
- OpenAI Embeddings
- Pinecone
- REST API

## Architecture

```text
                    DOCUMENT INGESTION

PDF
 │
 ▼
Text Extraction
 │
 ▼
Text Chunking
 │
 ├───────────────► MySQL
 │                  └── Document Chunks
 │
 ▼
OpenAI Embeddings
 │
 ▼
Pinecone
 └── Vector + Metadata


                    RAG QUERY

User Question
 │
 ▼
OpenAI Embedding
 │
 ▼
Pinecone Semantic Search
 │
 ▼
Relevant Chunk IDs
 │
 ▼
MySQL
 │
 ▼
Relevant Document Chunks
 │
 ▼
OpenAI LLM
 │
 ▼
Grounded Answer + Sources
```

## API Endpoints

### Upload Document

```http
POST /api/documents
```

Uploads a PDF and processes it through the document ingestion pipeline.

Flow:

```text
PDF → Extract → Chunk → MySQL → Embedding → Pinecone
```

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/documents \
  -F "file=@document.pdf"
```

### Semantic Search

```http
POST /api/search
```

Searches uploaded documents using semantic similarity.

Request:

```json
{
    "query": "What is artificial intelligence?",
    "top_k": 5
}
```

Flow:

```text
Question → Embedding → Pinecone → Relevant Chunks
```

### Ask

```http
POST /api/ask
```

Runs the complete RAG pipeline and generates an answer using retrieved document context.

Request:

```json
{
    "query": "What is artificial intelligence?",
    "top_k": 5
}
```

Response:

```json
{
    "answer": "Artificial Intelligence is...",
    "sources": [
        {
            "score": 0.6,
            "document_id": 8,
            "document_name": "AI_Concepts_Guide.pdf",
            "chunk_id": 22,
            "chunk_index": 0
        }
    ]
}
```

## Data Storage

### MySQL

MySQL stores application data and document content.

```text
documents
    │
    └── document_chunks
```

`document_chunks` stores the actual chunk content.

### Pinecone

Pinecone stores:

- Embedding vectors
- Document ID
- Chunk ID
- Chunk index
- Document name

MySQL remains the source of truth for the actual document content.

## RAG Process

The project follows two main pipelines.

### Document Ingestion

```text
PDF
 ↓
Extract Text
 ↓
Chunk Text
 ↓
Store Chunks in MySQL
 ↓
Generate Embeddings
 ↓
Store Vectors in Pinecone
```

### Question Answering

```text
Question
 ↓
Generate Query Embedding
 ↓
Pinec
```

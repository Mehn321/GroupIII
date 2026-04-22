# AI-Powered Chatbot with Database Knowledge Base

A comprehensive customer support chatbot system that combines semantic search with a database-driven knowledge base. The chatbot searches for matching questions in its database first, and only generates fallback responses if no suitable match is found.

## 🎯 Features

- **Database-Driven Knowledge Base**: Store and manage Q&A pairs in a structured database
- **Semantic Search**: Intelligent matching using Levenshtein distance, cosine similarity, and Jaccard similarity
- **Category Organization**: Organize Q&As by topics (Orders, Shipping, Returns, Payments, etc.)
- **Admin Panel**: Easy-to-use interface for managing questions and answers
- **User Chatbot**: Clean, modern chat interface for customers
- **Analytics**: Track query statistics and match success rates
- **Scalable Architecture**: Easy to extend with new Q&As and features

## 🏗️ System Architecture

### Database Schema

#### **Categories Table**
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```
Stores categories like "Orders & Tracking", "Shipping & Delivery", etc.

#### **Questions Table**
```sql
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    category_id INT,
    keywords VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_keywords (keywords)
)
```
Stores questions with associated keywords for better search.

#### **Answers Table**
```sql
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question (question_id)
)
```
Stores answers linked to questions (one-to-one relationship).

#### **Query Logs Table**
```sql
CREATE TABLE query_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    query_text TEXT NOT NULL,
    source ENUM('knowledge_base', 'fallback') NOT NULL,
    matched_question_id INT,
    similarity_score DECIMAL(3, 3),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_source (source),
    INDEX idx_created (created_at),
    FOREIGN KEY (matched_question_id) REFERENCES questions(id) ON DELETE SET NULL
)
```
Tracks all queries and matches for analytics.

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- XAMPP (or any local server)

### Installation Steps

1. **Navigate to the project directory:**
   ```bash
   cd c:\xampp\htdocs\chatbot
   ```

2. **Set up the database:**
   - Open `http://localhost/chatbot/setup_db.php` in your browser
   - This creates all necessary tables and categories
   - You'll see confirmation messages for each table created

3. **Insert sample data:**
   - Open `http://localhost/chatbot/insert_sample_data.php`
   - This populates the database with 30+ realistic shopping-related Q&A pairs
   - The sample data covers all 7 categories

4. **Access the applications:**
   - **User Chatbot**: `http://localhost/chatbot/index.html`
   - **Admin Panel**: `http://localhost/chatbot/admin.html`

## 📋 Database Query Process

### How the Chatbot Processes Queries

1. **User Submission**: User enters a question in the chat interface
2. **API Call**: Frontend sends query to `api/chatbot.php`
3. **Semantic Search**: System calculates similarity between user query and all stored questions
4. **Similarity Scoring**:
   - **Levenshtein Distance (20% weight)**: String edit distance
   - **Cosine Similarity (50% weight)**: Word vector similarity
   - **Jaccard Similarity (30% weight)**: Set-based overlap
5. **Threshold Check**: If best match score ≥ 60%, return database answer
6. **Fallback**: If no good match, return AI-generated response
7. **Logging**: Query and match results are logged for analytics

### Semantic Similarity Algorithm

```
Combined_Score = (Levenshtein × 0.2) + (Cosine × 0.5) + (Jaccard × 0.3)
```

The system removes common stop words before comparison for better semantic matching.

## 🛠️ Core Components

### 1. **Database.php** - Database Connection
Singleton pattern for database connections, prepared statements, and safe query execution.

**Key Methods:**
- `getInstance()`: Get singleton instance
- `prepare()`: Prepare safe SQL statements
- `query()`: Execute direct queries
- `escape()`: Escape strings safely

### 2. **KnowledgeBase.php** - Q&A Management
Handles all CRUD operations on questions and answers.

**Key Methods:**
- `addQA()`: Insert new question-answer pair
- `updateQA()`: Modify existing Q&A
- `deleteQA()`: Remove Q&A pair
- `getAllQA()`: Retrieve all Q&As
- `searchByCategory()`: Filter by category
- `searchQuestions()`: Full-text search

**Example Usage:**
```php
$kb = new KnowledgeBase();

// Add new Q&A
$id = $kb->addQA(
    "How do I track my order?",
    "You can track your order in your account dashboard.",
    1, // category_id
    "track, order, status" // keywords
);

// Get all Q&As
$all_qa = $kb->getAllQA();

// Search by category
$qa_list = $kb->searchByCategory(2);
```

### 3. **SemanticSearch.php** - Similarity Matching
Implements multiple similarity algorithms for semantic search.

**Key Methods:**
- `calculateSimilarity()`: Compute combined similarity score
- `findSimilarQuestions()`: Get all matches above threshold
- `findBestMatch()`: Get single best match
- `levenshteinSimilarity()`: String distance-based similarity
- `cosineSimilarity()`: Vector-based similarity
- `jaccardSimilarity()`: Set-based similarity

**Example Usage:**
```php
$search = new SemanticSearch();

// Find best match
$match = $search->findBestMatch("Where's my order?", 0.6);

// Get top 5 matches
$matches = $search->findSimilarQuestions(
    "How long does shipping take?",
    0.6, // threshold
    5    // limit
);
```

### 4. **ChatbotEngine.php** - Query Processing
Main logic for processing user queries and generating responses.

**Key Methods:**
- `processQuery()`: Main query handler
- `generateFallbackResponse()`: Create AI response when no match found
- `logQuery()`: Track all queries
- `getStatistics()`: Get analytics

**Example Usage:**
```php
$chatbot = new ChatbotEngine();

$response = $chatbot->processQuery("Can I return my purchase?");

// Response structure:
// {
//   "success": true,
//   "user_query": "Can I return my purchase?",
//   "source": "knowledge_base",
//   "answer": "...",
//   "match_score": 0.85,
//   "matched_question": "What is your return policy?",
//   "category": "Returns & Refunds",
//   "matches": [...] // All similar matches
// }
```

## 📡 API Endpoints

### Chatbot API (`api/chatbot.php`)

#### **Query Endpoint** (POST)
```
POST /api/chatbot.php?action=query
Content-Type: application/json

{
    "query": "How long does shipping take?"
}

Response:
{
    "success": true,
    "user_query": "How long does shipping take?",
    "source": "knowledge_base",
    "answer": "Standard shipping takes 5-7 business days...",
    "match_score": 0.92,
    "matched_question": "How long does shipping take?",
    "category": "Shipping & Delivery",
    "matches": [
        {
            "id": 5,
            "question": "How long does shipping take?",
            "answer": "...",
            "similarity_score": 0.92,
            "category": "Shipping & Delivery"
        }
    ],
    "timestamp": "2025-01-28 10:30:45"
}
```

#### **Statistics Endpoint** (GET)
```
GET /api/chatbot.php?action=stats

Response:
{
    "success": true,
    "data": {
        "total_queries": 42,
        "total_questions_in_kb": 30,
        "queries_by_source": {
            "knowledge_base": 38,
            "fallback": 4
        },
        "average_match_score": 0.847
    }
}
```

### Admin API (`api/admin.php`)

#### **Get All Q&As** (GET)
```
GET /api/admin.php?action=all

Response:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "question_text": "...",
            "answer_text": "...",
            "category": "Orders & Tracking",
            "category_id": 1,
            "keywords": "...",
            "created_at": "2025-01-28 10:15:30",
            "updated_at": "2025-01-28 10:15:30"
        }
    ],
    "count": 30
}
```

#### **Get Single Q&A** (GET)
```
GET /api/admin.php?action=get&id=5
```

#### **Get Categories** (GET)
```
GET /api/admin.php?action=categories
```

#### **Add Q&A** (POST)
```
POST /api/admin.php?action=add
Content-Type: application/json

{
    "question": "What payment methods do you accept?",
    "answer": "We accept Visa, Mastercard, PayPal...",
    "category_id": 4,
    "keywords": "payment, credit card, paypal"
}

Response:
{
    "success": true,
    "message": "Q&A pair created successfully",
    "id": 31
}
```

#### **Update Q&A** (PUT)
```
PUT /api/admin.php?action=update
Content-Type: application/json

{
    "id": 5,
    "question": "How long does shipping take?",
    "answer": "...",
    "category_id": 2,
    "keywords": "..."
}
```

#### **Delete Q&A** (DELETE)
```
DELETE /api/admin.php?action=delete&id=5
```

## 👨‍💻 Adding New Questions and Answers

### Method 1: Using Admin Panel

1. Go to `admin.html`
2. Click "Add New Q&A" tab
3. Fill in:
   - Question
   - Answer
   - Category
   - Keywords (optional, comma-separated)
4. Click "Add Q&A Pair"

### Method 2: Using API (Programmatic)

```javascript
// JavaScript example
const newQA = {
    question: "Do you offer gift wrapping?",
    answer: "Yes, complimentary gift wrapping is available at checkout.",
    category_id: 5,
    keywords: "gift, wrap, wrapping, gift wrap"
};

fetch('api/admin.php?action=add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(newQA)
})
.then(r => r.json())
.then(data => console.log(data));
```

### Method 3: Direct PHP Code

```php
require_once 'includes/Database.php';
require_once 'includes/KnowledgeBase.php';

$kb = new KnowledgeBase();

$question_id = $kb->addQA(
    "Do you offer international shipping?",
    "Yes, we ship to 195+ countries worldwide.",
    2, // Shipping & Delivery category
    "international, worldwide, shipping"
);
```

## 🎨 User Interfaces

### Chatbot Interface (`index.html`)
- Clean, modern design with gradient background
- Real-time chat with typing indicators
- Example queries for quick start
- Match source indicator (Knowledge Base vs. AI-generated)
- Responsive design for mobile and desktop

### Admin Panel (`admin.html`)
- **Q&A Management Tab**: View, search, edit, delete Q&As
- **Add New Q&A Tab**: Form to create new questions
- **Statistics Tab**: View analytics and system info
- Edit modal for updating existing Q&As
- Real-time feedback messages

## 📊 Sample Data

The system comes pre-populated with 30+ realistic shopping-related Q&A pairs across 7 categories:

1. **Orders & Tracking** (4 Q&As)
   - Order tracking, confirmation, modification, order numbers

2. **Shipping & Delivery** (5 Q&As)
   - Shipping times, options, international shipping, address changes

3. **Returns & Refunds** (6 Q&As)
   - Return policy, process, timeframes, sale items, non-returnable items

4. **Payment & Billing** (6 Q&As)
   - Payment methods, security, declined cards, installment plans

5. **Product Availability** (4 Q&As)
   - Stock status, pre-orders, restock notifications

6. **Account & Login** (4 Q&As)
   - Account creation, password reset, guest checkout

7. **Warranty & Support** (4 Q&As)
   - Warranty coverage, claims, damage, support contact

## ⚙️ Configuration

Edit `config.php` to customize:

```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chatbot_db');

// Similarity threshold (0-1, 0.6 = 60%)
define('SIMILARITY_THRESHOLD', 0.6);

// Max results to return
define('MAX_RESULTS', 5);
```

### Adjusting Similarity Threshold

- **0.4**: Very lenient, returns many matches (higher false positives)
- **0.6**: Balanced (default, recommended)
- **0.8**: Strict, only very similar questions match (higher false negatives)
- **1.0**: Perfect match only

## 🔐 Security Considerations

### Current Implementation
- ✅ Prepared statements (prevent SQL injection)
- ✅ String escaping for user inputs
- ✅ CORS headers for API protection
- ✅ UTF-8 character encoding

### Production Recommendations
- ⚠️ Add authentication to admin API (currently open)
- ⚠️ Implement rate limiting on chatbot API
- ⚠️ Add HTTPS/SSL encryption
- ⚠️ Implement CSRF tokens for form submissions
- ⚠️ Add input validation and sanitization
- ⚠️ Log sensitive operations

Example admin authentication:
```php
// Add to api/admin.php
function checkAdminAccess() {
    // Check session token or API key
    if (!isset($_SESSION['admin_token']) || $_SESSION['admin_token'] !== 'YOUR_SECRET_TOKEN') {
        return false;
    }
    return true;
}
```

## 📈 Scalability

### Handling Growing Knowledge Base

**Optimizations for large datasets:**

1. **Database Indexing**: Already implemented on:
   - `idx_category`: Fast category filtering
   - `idx_keywords`: Fast keyword search
   - `idx_question`: Answer lookups

2. **Pagination**: Modify `SemanticSearch::findSimilarQuestions()` to limit comparisons:
   ```php
   // Only compare against recent 500 questions
   $limit = 500;
   $all_questions = array_slice($all_questions, 0, $limit);
   ```

3. **Caching**: Cache frequently accessed Q&As:
   ```php
   // Add to config
   define('CACHE_DIR', __DIR__ . '/cache');
   ```

4. **Full-Text Search**: Replace semantic search with MySQL FULLTEXT for large datasets:
   ```sql
   ALTER TABLE questions ADD FULLTEXT INDEX ft_questions (question_text);
   SELECT * FROM questions WHERE MATCH(question_text) AGAINST('query');
   ```

### Database Growth Monitoring

Monitor these metrics:
- Total questions: Currently ~30, plan for 1000+
- Queries per day: Track in query_logs
- Average response time: Should stay under 200ms

## 🐛 Troubleshooting

### Database Connection Error
**Issue**: "Connection failed"
**Solution**: 
- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database `chatbot_db` exists

### No Results from Semantic Search
**Issue**: All queries return fallback responses
**Solution**:
- Increase SIMILARITY_THRESHOLD in config.php
- Add keywords to questions for better matching
- Verify database has Q&A pairs via admin panel

### Admin Panel Not Loading
**Issue**: Blank page or JS errors
**Solution**:
- Check browser console for errors (F12)
- Verify API endpoints are accessible
- Clear browser cache and reload

### Slow Query Response
**Issue**: Takes >1 second to respond
**Solution**:
- Check database indexes
- Reduce number of questions being compared
- Implement query result caching

## 📝 File Structure

```
c:/xampp/htdocs/chatbot/
├── config.php                    # Configuration file
├── index.html                    # User chatbot interface
├── admin.html                    # Admin panel interface
├── setup_db.php                  # Database initialization
├── insert_sample_data.php        # Sample data insertion
├── README.md                     # This file
│
├── includes/
│   ├── Database.php             # Database connection class
│   ├── KnowledgeBase.php        # Q&A CRUD operations
│   ├── SemanticSearch.php       # Similarity matching
│   └── ChatbotEngine.php        # Main chatbot logic
│
└── api/
    ├── chatbot.php              # User chatbot API
    └── admin.php                # Admin management API
```

## 🔄 Future Enhancements

1. **AI Fallback Integration**
   - Connect to OpenAI API for intelligent fallback responses
   - Fine-tune AI responses based on category and context

2. **Multi-Language Support**
   - Detect user language and provide answers in that language
   - Translate questions for broader semantic matching

3. **Machine Learning**
   - Track which Q&As are most helpful
   - Automatically optimize similarity thresholds
   - Predict user intent from questions

4. **Advanced Analytics**
   - Dashboard with charts and metrics
   - Identify unanswered questions
   - A/B testing different answers

5. **User Feedback**
   - Rate answer helpfulness
   - Suggest improvements
   - Feedback-driven knowledge base optimization

6. **Integration Features**
   - Slack/Discord bot integration
   - WhatsApp Business integration
   - Email support integration

7. **Advanced Search**
   - Fuzzy matching for typo tolerance
   - Entity recognition (product names, dates)
   - Contextual conversation memory

## 📞 Support & Contact

For issues or questions about the system:
1. Check troubleshooting section above
2. Review API documentation for endpoint details
3. Check database logs for errors
4. Review browser console (F12) for frontend issues

## 📄 License

This chatbot system is provided as-is for internal use.

---

**Last Updated**: January 28, 2025
**Version**: 1.0.0

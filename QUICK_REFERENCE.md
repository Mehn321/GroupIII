# 🚀 Quick Reference Card

## FILES CHEAT SHEET

### Start Here
```
setup_db.php              → Run once to create database
insert_sample_data.php    → Run once to add sample Q&As
index.html               → User chatbot interface
admin.html               → Admin management panel
```

### Configuration
```
config.php               → Database credentials & settings
```

### Backend Classes (includes/)
```
Database.php             → Database connection
KnowledgeBase.php        → Q&A CRUD operations
SemanticSearch.php       → Similarity matching
ChatbotEngine.php        → Main chatbot logic
```

### APIs (api/)
```
chatbot.php             → User query API
admin.php               → Admin management API
```

### Documentation
```
QUICKSTART.md           → 5-min setup (START HERE!)
README.md               → Complete reference
API.md                  → API documentation
DATABASE_SCHEMA.md      → Database design
```

---

## QUICK COMMANDS

### First Time Setup
```bash
# Step 1: Create database
http://localhost/chatbot/setup_db.php

# Step 2: Load sample data
http://localhost/chatbot/insert_sample_data.php

# Step 3: Start using!
http://localhost/chatbot/index.html
```

### Access Points
```
User Interface:     http://localhost/chatbot/index.html
Admin Panel:        http://localhost/chatbot/admin.html
Chatbot API:        http://localhost/chatbot/api/chatbot.php
Admin API:          http://localhost/chatbot/api/admin.php
```

### Test Chatbot API
```bash
curl -X POST http://localhost/chatbot/api/chatbot.php?action=query \
  -H "Content-Type: application/json" \
  -d '{"query":"How long does shipping take?"}'
```

### Get Statistics
```bash
curl http://localhost/chatbot/api/chatbot.php?action=stats
```

### Add Q&A via API
```bash
curl -X POST http://localhost/chatbot/api/admin.php?action=add \
  -H "Content-Type: application/json" \
  -d '{
    "question": "Your question?",
    "answer": "Your answer.",
    "category_id": 1,
    "keywords": "search, terms"
  }'
```

---

## DIRECTORY STRUCTURE

```
chatbot/
├── config.php                    (Database & API settings)
├── setup_db.php                  (Database creation)
├── insert_sample_data.php        (Sample data)
├── index.html                    (Chat interface)
├── admin.html                    (Admin panel)
├── includes/
│   ├── Database.php
│   ├── KnowledgeBase.php
│   ├── SemanticSearch.php
│   └── ChatbotEngine.php
├── api/
│   ├── chatbot.php
│   └── admin.php
└── Documentation/
    ├── QUICKSTART.md             ← START HERE!
    ├── README.md
    ├── API.md
    ├── DATABASE_SCHEMA.md
    └── ... (other docs)
```

---

## KEY SETTINGS (config.php)

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chatbot_db');

// Similarity threshold (0-1, higher = stricter)
define('SIMILARITY_THRESHOLD', 0.6);

// Max results to return
define('MAX_RESULTS', 5);
```

---

## API ENDPOINTS SUMMARY

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/api/chatbot.php?action=query` | Process user query |
| GET | `/api/chatbot.php?action=stats` | Get statistics |
| GET | `/api/admin.php?action=all` | Get all Q&As |
| GET | `/api/admin.php?action=get&id=X` | Get single Q&A |
| GET | `/api/admin.php?action=categories` | Get categories |
| POST | `/api/admin.php?action=add` | Create Q&A |
| PUT | `/api/admin.php?action=update` | Update Q&A |
| DELETE | `/api/admin.php?action=delete&id=X` | Delete Q&A |

---

## DATABASE TABLES

```
CATEGORIES          (7 default categories)
├─ id
├─ name
└─ description

QUESTIONS           (30+ sample Q&As)
├─ id
├─ question_text
├─ category_id (FK)
└─ keywords

ANSWERS             (matched to questions)
├─ id
├─ question_id (FK)
└─ answer_text

QUERY_LOGS          (all queries tracked)
├─ id
├─ query_text
├─ source (KB or fallback)
├─ matched_question_id (FK)
└─ similarity_score
```

---

## SIMILARITY CALCULATION

```
Score = (Levenshtein × 0.2) + (Cosine × 0.5) + (Jaccard × 0.3)

If Score ≥ 60% → Return database answer
If Score < 60% → Return fallback response
```

---

## COMMON TASKS

### Add New Q&A
**Method 1 - Via Admin UI:**
1. Open admin.html
2. Go to "Add New Q&A" tab
3. Fill in question, answer, category
4. Click "Add Q&A Pair"

**Method 2 - Via API:**
```bash
curl -X POST http://localhost/chatbot/api/admin.php?action=add \
  -H "Content-Type: application/json" \
  -d '{"question":"Q?","answer":"A.","category_id":1,"keywords":"k1,k2"}'
```

### Update Q&A
**Via Admin UI:**
1. Open admin.html
2. Find Q&A in table
3. Click "Edit"
4. Modify fields
5. Click "Update Q&A"

### Delete Q&A
**Via Admin UI:**
1. Open admin.html
2. Find Q&A in table
3. Click "Delete"
4. Confirm deletion

### Test Query
**Via Chatbot:**
1. Open index.html
2. Type question
3. Click Send
4. See response

**Via API:**
```bash
curl -X POST http://localhost/chatbot/api/chatbot.php?action=query \
  -H "Content-Type: application/json" \
  -d '{"query":"Your question here"}'
```

---

## CATEGORIES

```
1. Orders & Tracking        (Order status, tracking, modification)
2. Shipping & Delivery      (Shipping times, options, addresses)
3. Returns & Refunds        (Return policy, process, timeframes)
4. Payment & Billing        (Payment methods, security, issues)
5. Product Availability     (Stock status, pre-orders, availability)
6. Account & Login          (Account creation, password, profiles)
7. Warranty & Support       (Warranty, claims, damage, support)
```

---

## TROUBLESHOOTING

| Issue | Solution |
|-------|----------|
| "Connection failed" | Check MySQL running; verify config.php |
| No responses | Run insert_sample_data.php; check database |
| Admin not working | Check browser console (F12); clear cache |
| API returns errors | Check request format; verify endpoints |
| Slow performance | Check database indices; reduce questions |

---

## STATUS CODES

```
200 OK                  Query succeeded
201 Created             Q&A created successfully
400 Bad Request         Missing or invalid parameters
404 Not Found           Q&A doesn't exist
500 Server Error        Database or execution error
```

---

## RESPONSE FORMAT

### Successful Query
```json
{
  "success": true,
  "source": "knowledge_base",
  "answer": "Answer text here...",
  "match_score": 0.92,
  "category": "Shipping & Delivery"
}
```

### Fallback Response
```json
{
  "success": true,
  "source": "fallback",
  "answer": "Auto-generated response...",
  "matches": []
}
```

### Error
```json
{
  "success": false,
  "error": "Error message here"
}
```

---

## DOCUMENTATION MAP

| Document | When to Read | Read Time |
|----------|--------------|-----------|
| QUICKSTART.md | First time setup | 5 min |
| README.md | Understand system | 20 min |
| API.md | For API integration | 15 min |
| DATABASE_SCHEMA.md | Database details | 15 min |

---

## FREQUENTLY CHANGED

### Similarity Threshold
**File**: config.php
**Change**: Line with `define('SIMILARITY_THRESHOLD', 0.6);`
- Lower = more lenient (more results)
- Higher = more strict (fewer, better results)

### UI Colors
**Files**: index.html, admin.html
**Change**: Look for `<style>` section
- Background: `linear-gradient(135deg, #667eea 0%, #764ba2 100%);`
- Text: Color values in CSS

### Sample Data
**File**: insert_sample_data.php
**Change**: The `$sample_data` array
- Add/modify/remove Q&A pairs before running

### Database Name
**File**: config.php
**Change**: `define('DB_NAME', 'chatbot_db');`

---

## PRODUCTION CHECKLIST

- [ ] Set SIMILARITY_THRESHOLD appropriately
- [ ] Update company name in UI
- [ ] Customize sample data
- [ ] Test all functionality
- [ ] Set up database backups
- [ ] Add authentication to admin API
- [ ] Configure rate limiting
- [ ] Set up HTTPS/SSL
- [ ] Review security settings
- [ ] Monitor performance
- [ ] Plan scaling strategy

---

## KEY FILES TO REMEMBER

```
📝 Setup:           setup_db.php, insert_sample_data.php
🎯 Use:             index.html, admin.html
⚙️  Config:         config.php
🔧 API:             api/chatbot.php, api/admin.php
📚 Learn:           README.md, API.md
```

---

## BACKUP & RECOVERY

### Backup Database
```bash
mysqldump -u root -p chatbot_db > backup.sql
```

### Restore Database
```bash
mysql -u root -p chatbot_db < backup.sql
```

---

## ONE-LINER TESTS

### Check API
```bash
curl -s http://localhost/chatbot/api/chatbot.php?action=stats | json_pp
```

### Test Query
```bash
curl -s -X POST http://localhost/chatbot/api/chatbot.php?action=query \
  -H "Content-Type: application/json" \
  -d '{"query":"help"}' | json_pp
```

### Get All Q&As
```bash
curl -s http://localhost/chatbot/api/admin.php?action=all | json_pp
```

---

**Print this card and keep it handy! 📋**

**Project Status**: ✅ COMPLETE & READY
**Next Step**: Read QUICKSTART.md

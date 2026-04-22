# 🎯 AI-Powered Chatbot - Quick Navigation

## 🚀 Start Here

### Just Getting Started?
👉 Read **[QUICKSTART.md](QUICKSTART.md)** (5 minutes)

### Want the Full Picture?
👉 Read **[GETTING_STARTED.md](GETTING_STARTED.md)** (visual overview)

### Need Full Details?
👉 Read **[README.md](README.md)** (comprehensive guide)

---

## 📋 Documentation Map

### For End Users
- **[index.html](index.html)** - Use the chatbot
- **[QUICKSTART.md](QUICKSTART.md)** - How to use

### For Administrators
- **[admin.html](admin.html)** - Manage Q&As
- **[README.md](README.md)** - How to manage knowledge base
- **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - Overview of what you can do

### For Developers
- **[API.md](API.md)** - API endpoints & integration
- **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Database design
- **[includes/](includes/)** - Core logic files
- **[api/](api/)** - REST API endpoints

### For System Admins
- **[README.md](README.md)** - Deployment & configuration
- **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Backup & recovery

---

## ⚡ Quick Actions

### First Time Setup
```
1. Open browser to: http://localhost/chatbot/setup_db.php
2. Then open: http://localhost/chatbot/insert_sample_data.php
3. Done! System is ready to use
```

### Access Applications
- **Chatbot**: http://localhost/chatbot/index.html
- **Admin Panel**: http://localhost/chatbot/admin.html

### Run API Tests
```bash
# Get response from chatbot
curl -X POST http://localhost/chatbot/api/chatbot.php?action=query \
  -H "Content-Type: application/json" \
  -d '{"query":"How long does shipping take?"}'

# Get statistics
curl http://localhost/chatbot/api/chatbot.php?action=stats
```

---

## 📚 Documentation Quick Reference

| Document | Purpose | Best For |
|----------|---------|----------|
| **QUICKSTART.md** | 5-minute setup guide | Anyone setting up for first time |
| **GETTING_STARTED.md** | Visual system overview | Understanding how it all works |
| **README.md** | Complete reference | Comprehensive learning & advanced topics |
| **API.md** | REST API reference | Developers integrating with the system |
| **DATABASE_SCHEMA.md** | Database documentation | DBAs & architects |
| **PROJECT_SUMMARY.md** | Project overview | Executives & decision makers |
| **FILE_LISTING.md** | File descriptions | Understanding project structure |

---

## 🔍 Find What You Need

### "How do I...?"

#### Use the Chatbot?
→ Open **[index.html](index.html)** and start asking questions

#### Add a New Q&A?
→ Open **[admin.html](admin.html)** → "Add New Q&A" tab → Fill form → Click "Add Q&A Pair"

#### Find API Examples?
→ Read **[API.md](API.md)** - Has cURL and JavaScript examples

#### Understand the database?
→ Read **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Has diagrams and explanations

#### Configure settings?
→ Edit **[config.php](config.php)** - Adjust thresholds, API keys, etc.

#### Change the UI colors?
→ Edit **[index.html](index.html)** or **[admin.html](admin.html)** - Look for `<style>` section

#### Deploy to production?
→ Read **[README.md](README.md)** section "Production Recommendations"

#### Troubleshoot problems?
→ Read **[README.md](README.md)** section "Troubleshooting"

#### Integrate with my system?
→ Read **[API.md](API.md)** - Full endpoint documentation

---

## 📊 System Overview

```
User Asks Question
        ↓
index.html (Chatbot UI)
        ↓
api/chatbot.php (API)
        ↓
ChatbotEngine → SemanticSearch → KnowledgeBase → Database
        ↓
Returns Answer
```

---

## 🎓 Learning Path

### Beginner Path (1-2 hours)
1. Read **[QUICKSTART.md](QUICKSTART.md)** (5 min)
2. Run setup scripts (2 min)
3. Use **[index.html](index.html)** chatbot (10 min)
4. Use **[admin.html](admin.html)** admin panel (15 min)
5. Read **[GETTING_STARTED.md](GETTING_STARTED.md)** (15 min)

### Intermediate Path (2-3 hours)
1. Complete Beginner Path
2. Read **[README.md](README.md)** sections:
   - Architecture
   - Database Schema
   - Core Components
3. Review code in **[includes/](includes/)** directory
4. Read **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)**

### Advanced Path (4+ hours)
1. Complete Intermediate Path
2. Read **[README.md](README.md)** entire document
3. Read **[API.md](API.md)** entire document
4. Review all code files with comments
5. Read **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** entire document
6. Plan customizations/extensions

---

## 🔗 Key Files at a Glance

### Configuration
```
config.php          ← Database & API settings
```

### Backend Classes
```
includes/
├── Database.php         ← Database connection
├── KnowledgeBase.php    ← Q&A CRUD operations
├── SemanticSearch.php   ← Smart matching (3 algorithms)
└── ChatbotEngine.php    ← Main chatbot logic
```

### APIs
```
api/
├── chatbot.php          ← User chatbot API
└── admin.php            ← Admin management API
```

### Frontend
```
index.html              ← Chatbot interface
admin.html              ← Admin interface
```

### Setup
```
setup_db.php            ← Create database (run once)
insert_sample_data.php  ← Load sample data (run once)
```

### Documentation
```
QUICKSTART.md           ← Quick start (5 min read)
GETTING_STARTED.md      ← Visual overview
README.md               ← Complete reference
API.md                  ← API documentation
DATABASE_SCHEMA.md      ← Database design
PROJECT_SUMMARY.md      ← Project overview
FILE_LISTING.md         ← File descriptions
```

---

## 🆘 Troubleshooting Quick Links

| Problem | Solution |
|---------|----------|
| "Connection failed" | Read: Troubleshooting in [README.md](README.md) |
| No responses from chatbot | Check: [QUICKSTART.md](QUICKSTART.md) - Troubleshooting |
| API not working | Review: [API.md](API.md) - Endpoint format |
| Admin panel not loading | See: [README.md](README.md) - Browser console errors |
| Slow performance | Check: [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Indexing |

---

## 📞 Need Help?

1. **Getting started?** → Read **[QUICKSTART.md](QUICKSTART.md)**
2. **How does it work?** → Read **[GETTING_STARTED.md](GETTING_STARTED.md)**
3. **Technical details?** → Read **[README.md](README.md)**
4. **API integration?** → Read **[API.md](API.md)**
5. **Database questions?** → Read **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)**

---

## ✅ Checklist to Get Started

- [ ] Read **QUICKSTART.md** (5 min)
- [ ] Open **setup_db.php** in browser
- [ ] Open **insert_sample_data.php** in browser
- [ ] Open **index.html** and ask a question
- [ ] Open **admin.html** and explore
- [ ] Read **GETTING_STARTED.md** for overview
- [ ] Read **README.md** for details
- [ ] Bookmark this file for reference

---

## 🎉 You're All Set!

Your complete AI chatbot system is ready to use!

**Next Step**: Open [QUICKSTART.md](QUICKSTART.md) and follow the 3-step setup.

---

**Need a specific file?** Check [FILE_LISTING.md](FILE_LISTING.md)

**Want system overview?** Read [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)

**Ready to code?** Check [README.md](README.md) and [API.md](API.md)

# 🚀 Quick Start Guide

Get your AI chatbot up and running in 5 minutes!

## Step 1: Initialize Database (1 minute)

1. Open your web browser
2. Navigate to: `http://localhost/chatbot/setup_db.php`
3. You should see green checkmarks ✓ for each table created:
   - ✓ Table 'categories' created successfully
   - ✓ Table 'questions' created successfully
   - ✓ Table 'answers' created successfully
   - ✓ Table 'query_logs' created successfully
   - ✓ Category 'Orders & Tracking' inserted
   - ✓ Category 'Shipping & Delivery' inserted
   - ... (7 total categories)

**Status**: Database is now ready!

## Step 2: Load Sample Data (2 minutes)

1. Navigate to: `http://localhost/chatbot/insert_sample_data.php`
2. Watch as 30+ realistic Q&A pairs are inserted
3. You'll see confirmation messages for each:
   - Orders & Tracking (4 Q&As)
   - Shipping & Delivery (5 Q&As)
   - Returns & Refunds (6 Q&As)
   - Payment & Billing (6 Q&As)
   - Product Availability (4 Q&As)
   - Account & Login (4 Q&As)
   - Warranty & Support (4 Q&As)

**Status**: Knowledge base is populated!

## Step 3: Start Chatting! (1 minute)

### Using the Chatbot
1. Navigate to: `http://localhost/chatbot/index.html`
2. Try these example questions:
   - "What is your return policy?"
   - "How long does shipping take?"
   - "How do I track my order?"
   - "Can I cancel my order?"

**What happens:**
- ✅ **Knowledge Base Match** (green indicator): Answer found in database with 80%+ match
- 💡 **Auto-generated Response** (blue indicator): No good match, fallback response generated

## Step 4: Manage Your Knowledge Base (1 minute)

### Using the Admin Panel
1. Navigate to: `http://localhost/chatbot/admin.html`
2. Explore these tabs:

#### **Q&A Management Tab**
- View all 30 sample questions and answers
- Search for specific questions
- Edit existing Q&As
- Delete Q&As you don't need

#### **Add New Q&A Tab**
Create new questions:
```
Question: "Do you offer gift wrapping?"
Answer: "Yes! Complimentary gift wrapping is available at checkout."
Category: "Product Availability"
Keywords: "gift, wrapping, wrap"
```

#### **Statistics Tab**
See system metrics:
- Total queries processed
- Questions in knowledge base
- Knowledge base match rate
- Average similarity score

---

## 📋 Example Conversations

### Example 1: Successful Database Match
```
User: "How long does shipping take?"
Response Source: 📚 Knowledge Base (92% match)
Answer: "Standard shipping typically takes 5-7 business days..."
```

### Example 2: Partial Match with Suggestions
```
User: "Can I get my money back?"
Response Source: 📚 Knowledge Base (78% match)
Matched: "What is your return policy?"
Answer: "We offer a 30-day money-back guarantee..."
```

### Example 3: No Match - Fallback Response
```
User: "Do you have this item in purple?"
Response Source: 💡 Auto-generated
Answer: "For product availability information, please check our website..."
```

---

## 🎯 Key Features to Try

### 1. **Semantic Matching**
The chatbot understands similar questions:
- "Track my order" ≈ "Where's my package?"
- "Return policy" ≈ "Can I send items back?"
- "How do I pay?" ≈ "Payment methods?"

### 2. **Category Organization**
Questions are grouped by topic:
- Orders & Tracking
- Shipping & Delivery
- Returns & Refunds
- Payment & Billing
- Product Availability
- Account & Login
- Warranty & Support

### 3. **Match Score Display**
Know how confident the chatbot is:
- 90%+ = Very sure it's the right answer
- 70-89% = Fairly sure
- Below 60% = No match, uses fallback

### 4. **Admin Control**
Easily maintain your knowledge base:
- Add new Q&As via web form
- Update existing answers
- Delete outdated information
- See real-time statistics

---

## 🔧 Configuration Tips

### Want Stricter Matching?
Edit `config.php`:
```php
define('SIMILARITY_THRESHOLD', 0.75); // Default is 0.6
```
- Higher = fewer, more accurate matches
- Lower = more matches, some may be inaccurate

### Want More Results Displayed?
Edit `config.php`:
```php
define('MAX_RESULTS', 10); // Default is 5
```

### Add More Sample Data?
1. Edit `insert_sample_data.php`
2. Add more Q&A items to the `$sample_data` array
3. Run the script again

---

## 📱 Browser Compatibility

Works perfectly in:
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers

---

## 🆘 Quick Troubleshooting

### Issue: "Connection failed" when setting up database
**Solution**: 
1. Make sure XAMPP MySQL is running (green next to MySQL)
2. Check credentials in `config.php`
3. Restart XAMPP

### Issue: No responses from chatbot
**Solution**:
1. Check that sample data was inserted
2. Verify database has questions via admin panel
3. Try asking exact questions from database

### Issue: All queries show auto-generated responses
**Solution**:
1. Increase `SIMILARITY_THRESHOLD` in `config.php` to something lower (e.g., 0.4)
2. Check that sample data inserted successfully
3. Add more diverse keywords to questions

### Issue: Admin panel not working
**Solution**:
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify API URLs are correct
4. Clear browser cache and refresh

---

## 📊 Next Steps

After the initial setup:

1. **Customize Sample Data**
   - Edit questions to match your exact products/services
   - Add category-specific Q&As
   - Include your company's policies

2. **Add Branding**
   - Change colors in CSS
   - Update company name in headers
   - Customize welcome messages

3. **Integrate Authentication**
   - Add login to admin panel
   - Restrict API access
   - Track admin actions

4. **Connect to Real Backend**
   - Link to actual order tracking system
   - Integrate payment gateway
   - Connect to inventory system

5. **Deploy to Production**
   - Move from localhost to live server
   - Set up HTTPS/SSL
   - Configure backups
   - Monitor performance

---

## 🎓 Learning Resources

### Understanding the Architecture
- Read `README.md` for detailed documentation
- Check `includes/` folder for class documentation
- Review `api/` endpoints in README

### Customizing Behavior
- Edit similarity algorithms in `SemanticSearch.php`
- Modify fallback responses in `ChatbotEngine.php`
- Adjust database queries in `KnowledgeBase.php`

### Extending Features
- Add new database fields in schema
- Create new API endpoints
- Integrate external services
- Add user authentication

---

## 📞 API Reference (Quick)

### Ask Chatbot a Question
```bash
curl -X POST http://localhost/chatbot/api/chatbot.php?action=query \
  -H "Content-Type: application/json" \
  -d '{"query":"How do I return an item?"}'
```

### Get Statistics
```bash
curl http://localhost/chatbot/api/chatbot.php?action=stats
```

### Get All Q&As
```bash
curl http://localhost/chatbot/api/admin.php?action=all
```

### Add New Q&A
```bash
curl -X POST http://localhost/chatbot/api/admin.php?action=add \
  -H "Content-Type: application/json" \
  -d '{
    "question":"New question?",
    "answer":"New answer.",
    "category_id":1,
    "keywords":"key,words"
  }'
```

---

## ✅ Success Checklist

- [ ] Database tables created (setup_db.php)
- [ ] Sample data inserted (insert_sample_data.php)
- [ ] Chatbot interface loads (index.html)
- [ ] Can ask questions and get responses
- [ ] Admin panel loads (admin.html)
- [ ] Can view Q&As in admin panel
- [ ] Can add new Q&A from admin
- [ ] Statistics show queries tracked

**All done?** 🎉 Your chatbot is ready to use!

---

**Estimated Time**: 5-10 minutes total setup
**Next**: Read README.md for advanced features and customization

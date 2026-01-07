# Sample Data Structure

## Example Scenario

### Class Setup:
- **Class A** (ID: 1) has:
  - Students: Ahmed Ali (ID: 1), Fatima Khan (ID: 2), Hassan Ahmed (ID: 3)
  - Subjects: Mathematics (ID: 1), English (ID: 2), Science (ID: 3)

### Exam:
- Mid-term 2024 (ID: 1)

---

## Step-by-Step Form Filling

### Initial State:
```
Form Fields:
- Exam: [Select Exam ▼]
- Class: [Select Class ▼]
- Student: [hidden]
- Subjects Table: [hidden]
- Submit Button: [disabled]
```

### After Selecting Exam = "Mid-term 2024" & Class = "Class A":

**AJAX Request:**
```
GET /exam_result/ajax/class-data/1
```

**AJAX Response:**
```json
{
  "success": true,
  "students": [
    {"id": 1, "student_name": "Ahmed Ali"},
    {"id": 2, "student_name": "Fatima Khan"},
    {"id": 3, "student_name": "Hassan Ahmed"}
  ],
  "subjects": [
    {"id": 1, "name": "Mathematics"},
    {"id": 2, "name": "English"},
    {"id": 3, "name": "Science"}
  ]
}
```

### Updated Form:
```
Form Fields:
- Exam: [Mid-term 2024 ✓]
- Class: [Class A ✓]
- Student: [Select Student ▼] ← Now visible
  Options: Ahmed Ali, Fatima Khan, Hassan Ahmed
- Subjects Table: ← Now visible
  | Subject Name | Total Marks | Obtained Marks |
  | Mathematics  | [____]      | [____]         |
  | English      | [____]      | [____]         |
  | Science      | [____]      | [____]         |
- Submit Button: [Save All Results] ← Now enabled
```

### User Selects Student = "Ahmed Ali" and Fills Marks:
```
| Subject Name | Total Marks | Obtained Marks |
| Mathematics  | 100         | 85             |
| English      | 50          | 42             |
| Science      | 75          | 60             |
```

---

## Form Submission

### HTML Form Data (as sent to server):
```
POST /exam_result

_token: abc123def456xyz789...
exam_id: 1
class_id: 1
student_id: 1
marks[1][subject_id]: 1
marks[1][total_marks]: 100
marks[1][obtained_marks]: 85
marks[2][subject_id]: 2
marks[2][total_marks]: 50
marks[2][obtained_marks]: 42
marks[3][subject_id]: 3
marks[3][total_marks]: 75
marks[3][obtained_marks]: 60
```

---

## Database Records Created

After form submission, 3 records are created in `exam_results` table:

| id | exam_id | student_id | subject_id | class_id | total_marks | obtained_marks | created_at | updated_at |
|----|---------|------------|------------|----------|-------------|----------------|-----------|-----------|
| 1  | 1       | 1          | 1          | 1        | 100         | 85             | 2024-01-01 10:30:00 | 2024-01-01 10:30:00 |
| 2  | 1       | 1          | 2          | 1        | 50          | 42             | 2024-01-01 10:30:00 | 2024-01-01 10:30:00 |
| 3  | 1       | 1          | 3          | 1        | 75          | 60             | 2024-01-01 10:30:00 | 2024-01-01 10:30:00 |

### Unique Constraints (Enforced):
Each record is UNIQUE by: `(student_id, subject_id, exam_id)`
- Record 1: (1, 1, 1) ✓ Unique
- Record 2: (1, 2, 1) ✓ Unique
- Record 3: (1, 3, 1) ✓ Unique

---

## Duplicate Prevention Example

### Scenario: User tries same entry again

**Form Data:**
```
exam_id: 1
class_id: 1
student_id: 1
marks[1][subject_id]: 1
marks[1][total_marks]: 100
marks[1][obtained_marks]: 85
... (same data)
```

**Controller Logic:**
```php
foreach ($request->marks as $mark) {
    // Check if record exists
    $exists = ExamResult::where([
        'student_id' => 1,
        'subject_id' => 1,  // or 2, or 3
        'exam_id' => 1,
    ])->exists();
    
    if (!$exists) {
        ExamResult::create([...]);
        $created++;
    } else {
        $skipped++;  // Increment skipped counter
    }
}
```

**Result:**
```
Success message: "Exam results recorded: 0 created, 3 skipped (already exist)."
No duplicate records in database ✓
```

---

## Multiple Student Example

If user enters marks for a different student in the same class:

### Second Entry:
```
- Exam: Mid-term 2024
- Class: Class A
- Student: Fatima Khan (ID: 2)  ← Different student
- Marks: Same subjects, different marks
```

**Records Created:**
| id | exam_id | student_id | subject_id | class_id | total_marks | obtained_marks |
|----|---------|------------|------------|----------|-------------|----------------|
| 4  | 1       | 2          | 1          | 1        | 100         | 88             |
| 5  | 1       | 2          | 2          | 1        | 50          | 47             |
| 6  | 1       | 2          | 3          | 1        | 75          | 70             |

**Unique Constraints:**
- Record 4: (2, 1, 1) ✓ Unique (different student_id than Record 1)
- Record 5: (2, 2, 1) ✓ Unique (different student_id than Record 2)
- Record 6: (2, 3, 1) ✓ Unique (different student_id than Record 3)

All records coexist in database - no conflicts!

---

## Different Exam Example

If user enters results for a DIFFERENT exam:

### Third Entry:
```
- Exam: Final Exam 2024 (ID: 2)  ← Different exam
- Class: Class A
- Student: Ahmed Ali (ID: 1)  ← Same student
- Marks: Different marks
```

**Record Created:**
| id | exam_id | student_id | subject_id | class_id | total_marks | obtained_marks |
|----|---------|------------|------------|----------|-------------|----------------|
| 7  | 2       | 1          | 1          | 1        | 100         | 92             |

**Unique Constraint:**
- Record 7: (1, 1, 2) ✓ Unique (different exam_id than Record 1)

This is allowed because exam_id is part of the unique key. Same student, same subject, but DIFFERENT exam!

---

## Validation Flow Example

### Scenario: User tries invalid data

**Input:**
```
Obtained Marks: 110
Total Marks: 100
```

**Client-Side Validation:**
```javascript
if (obtainedMarks > totalMarks) {
    alert('Obtained marks cannot be greater than total marks');
    return; // Form doesn't submit
}
```

**Result:** User sees error, form doesn't submit ✓

---

## Table View - All Results

When visiting `/exam_result` (index), displays all results:

| Exam | Student | Subject | Class | Total | Obtained | Percentage |
|------|---------|---------|-------|-------|----------|-----------|
| Mid-term 2024 | Ahmed Ali | Mathematics | Class A | 100 | 85 | 85% |
| Mid-term 2024 | Ahmed Ali | English | Class A | 50 | 42 | 84% |
| Mid-term 2024 | Ahmed Ali | Science | Class A | 75 | 60 | 80% |
| Mid-term 2024 | Fatima Khan | Mathematics | Class A | 100 | 88 | 88% |
| Mid-term 2024 | Fatima Khan | English | Class A | 50 | 47 | 94% |
| Mid-term 2024 | Fatima Khan | Science | Class A | 75 | 70 | 93.3% |
| Final Exam 2024 | Ahmed Ali | Mathematics | Class A | 100 | 92 | 92% |

---

## Complete Data Tables

### exams table:
| id | name | exam_date | status |
|---|---|---|---|
| 1 | Mid-term 2024 | 2024-01-15 | active |
| 2 | Final Exam 2024 | 2024-03-30 | active |

### class_rooms table:
| id | class_name | session_id |
|---|---|---|
| 1 | Class A | 1 |
| 2 | Class B | 1 |

### students table:
| id | student_name | class_room_id | student_code |
|---|---|---|---|
| 1 | Ahmed Ali | 1 | STU001 |
| 2 | Fatima Khan | 1 | STU002 |
| 3 | Hassan Ahmed | 1 | STU003 |
| 4 | Zainab Hassan | 2 | STU004 |

### subjects table:
| id | subject_name | code |
|---|---|---|
| 1 | Mathematics | MATH |
| 2 | English | ENG |
| 3 | Science | SCI |
| 4 | History | HIST |

### class_subjects table (Junction):
| id | class_id | subject_id |
|---|---|---|
| 1 | 1 | 1 |  ← Class A has Math
| 2 | 1 | 2 |  ← Class A has English
| 3 | 1 | 3 |  ← Class A has Science
| 4 | 2 | 1 |  ← Class B has Math
| 5 | 2 | 2 |  ← Class B has English
| 6 | 2 | 4 |  ← Class B has History

### exam_results table (Result of our form):
| id | exam_id | student_id | subject_id | class_id | total_marks | obtained_marks | created_at |
|---|---|---|---|---|---|---|---|
| 1 | 1 | 1 | 1 | 1 | 100 | 85 | 2024-01-01 10:30:00 |
| 2 | 1 | 1 | 2 | 1 | 50 | 42 | 2024-01-01 10:30:00 |
| 3 | 1 | 1 | 3 | 1 | 75 | 60 | 2024-01-01 10:30:00 |

---

## Key Insights

1. **One form submission = Multiple records**
   - Single POST request creates 3 exam_result entries (one per subject)

2. **Unique constraint prevents duplicates**
   - (student_id, subject_id, exam_id) tuple must be unique
   - Same student can have different subjects
   - Same subject can have different students
   - Same student+subject can have different exams

3. **AJAX loads only necessary data**
   - Fetches only students of selected class
   - Fetches only subjects of selected class
   - No loading of all students/subjects in the world

4. **Progressive UI reveal**
   - Hidden sections appear only when data is available
   - Better UX than disabled dropdowns

5. **Clean data structure**
   - Proper relationships between all tables
   - No redundancy
   - Easy to query and report

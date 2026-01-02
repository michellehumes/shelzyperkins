// ==============================================
// LIFE OS - Google Sheets Dashboard
// ==============================================
//
// HOW TO USE:
// 1. Create a new Google Sheet
// 2. Go to Extensions > Apps Script
// 3. Delete any code there and paste this entire script
// 4. Save (Ctrl+S) and name the project "Life OS"
// 5. Run the "setupLifeOS" function (click Run button)
// 6. Grant permissions when prompted
// 7. Your Life OS is ready!
//
// ==============================================

function setupLifeOS() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();

  // Create all sheets
  createDashboardSheet(ss);
  createGoalsSheet(ss);
  createHabitsSheet(ss);
  createTasksSheet(ss);
  createLifeBalanceSheet(ss);
  createWeeklyReviewSheet(ss);
  createJournalSheet(ss);

  // Set Dashboard as active
  ss.setActiveSheet(ss.getSheetByName('Dashboard'));

  SpreadsheetApp.getUi().alert('Life OS setup complete! Your personal dashboard is ready.');
}

function createDashboardSheet(ss) {
  let sheet = ss.getSheetByName('Dashboard');
  if (!sheet) {
    sheet = ss.insertSheet('Dashboard', 0);
  } else {
    sheet.clear();
  }

  // Set column widths
  sheet.setColumnWidth(1, 200);
  sheet.setColumnWidth(2, 150);
  sheet.setColumnWidth(3, 200);
  sheet.setColumnWidth(4, 150);
  sheet.setColumnWidth(5, 200);
  sheet.setColumnWidth(6, 150);

  // Header
  sheet.getRange('A1:F1').merge();
  sheet.getRange('A1').setValue('LIFE OS DASHBOARD')
    .setFontSize(24)
    .setFontWeight('bold')
    .setHorizontalAlignment('center')
    .setBackground('#667eea')
    .setFontColor('white');

  sheet.getRange('A2:F2').merge();
  sheet.getRange('A2').setValue('=TEXT(TODAY(), "dddd, mmmm d, yyyy") & " | Week " & WEEKNUM(TODAY())')
    .setFontSize(12)
    .setHorizontalAlignment('center')
    .setBackground('#764ba2')
    .setFontColor('white');

  // Stats Cards Row
  const statsRow = 4;
  const stats = [
    ['Goal Progress', '=ROUND(AVERAGE(Goals!D2:D100), 0) & "%"'],
    ['Habits Today', '=COUNTIF(Habits!E2:E100, TRUE) & "/" & COUNTA(Habits!A2:A100)'],
    ['Tasks Done', '=COUNTIF(Tasks!E2:E100, TRUE) & "/" & COUNTIF(Tasks!A2:A100, "Today")'],
    ['Life Balance', '=ROUND(AVERAGE('Life Balance'!B2:B9), 1) & "/10"']
  ];

  stats.forEach((stat, i) => {
    const col = i * 2 + 1;
    sheet.getRange(statsRow, col).setValue(stat[0])
      .setFontWeight('bold')
      .setBackground('#f0f0f0');
    sheet.getRange(statsRow, col + 1).setValue(stat[1])
      .setFontSize(18)
      .setFontWeight('bold')
      .setFontColor('#FF6B6B');
  });

  // Today's Focus Section
  sheet.getRange('A6').setValue("TODAY'S TASKS")
    .setFontWeight('bold')
    .setFontSize(14)
    .setBackground('#FFE66D');
  sheet.getRange('A7').setValue('=FILTER(Tasks!B:B, Tasks!A:A="Today", Tasks!E:E=FALSE)')
    .setWrap(true);

  sheet.getRange('C6').setValue('HABITS')
    .setFontWeight('bold')
    .setFontSize(14)
    .setBackground('#4ECDC4');
  sheet.getRange('C7').setValue('=FILTER(Habits!A:A & " (" & Habits!C:C & " day streak)", Habits!B:B="Daily")')
    .setWrap(true);

  sheet.getRange('E6').setValue('TOP GOALS')
    .setFontWeight('bold')
    .setFontSize(14)
    .setBackground('#FF6B6B')
    .setFontColor('white');
  sheet.getRange('E7').setValue('=FILTER(Goals!B:B & " - " & Goals!D:D & "%", Goals!A:A="Yearly")')
    .setWrap(true);

  // Quick Links
  sheet.getRange('A15').setValue('QUICK NAVIGATION')
    .setFontWeight('bold')
    .setFontSize(14);
  sheet.getRange('A16').setValue('Goals | Habits | Tasks | Life Balance | Weekly Review | Journal')
    .setFontColor('#667eea');

  // Life Balance Mini Chart (text-based)
  sheet.getRange('A18').setValue('LIFE BALANCE OVERVIEW')
    .setFontWeight('bold')
    .setFontSize(14)
    .setBackground('#f0f0f0');
  sheet.getRange('A19').setValue('=ARRAYFORMULA('Life Balance'!A2:A9 & ": " & REPT("█", 'Life Balance'!B2:B9) & " " & 'Life Balance'!B2:B9 & "/10")');

  sheet.setFrozenRows(2);
}

function createGoalsSheet(ss) {
  let sheet = ss.getSheetByName('Goals');
  if (!sheet) {
    sheet = ss.insertSheet('Goals');
  } else {
    sheet.clear();
  }

  // Headers
  const headers = ['Timeframe', 'Goal', 'Category', 'Progress %', 'Target Date', 'Notes'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground('#667eea')
    .setFontColor('white');

  // Sample data
  const data = [
    ['Yearly', 'Build successful affiliate business', 'Career & Business', 40, '2026-12-31', ''],
    ['Yearly', 'Establish healthy daily routine', 'Health & Fitness', 60, '2026-12-31', ''],
    ['Yearly', 'Grow email list to 10,000 subscribers', 'Career & Business', 25, '2026-12-31', ''],
    ['Quarterly', 'Publish 30 blog posts', 'Career & Business', 33, '2026-03-31', ''],
    ['Quarterly', 'Set up all automation systems', 'Career & Business', 70, '2026-03-31', ''],
    ['Monthly', 'Write 10 product reviews', 'Career & Business', 50, '2026-01-31', ''],
    ['Monthly', 'Exercise 20 days this month', 'Health & Fitness', 10, '2026-01-31', ''],
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);

  // Data validation for Timeframe
  const timeframeRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Yearly', 'Quarterly', 'Monthly', 'Weekly'])
    .build();
  sheet.getRange('A2:A100').setDataValidation(timeframeRule);

  // Data validation for Category
  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Health & Fitness', 'Career & Business', 'Finances', 'Relationships', 'Personal Growth', 'Fun & Recreation', 'Physical Environment', 'Contribution'])
    .build();
  sheet.getRange('C2:C100').setDataValidation(categoryRule);

  // Progress bar conditional formatting
  const progressRange = sheet.getRange('D2:D100');
  const rules = sheet.getConditionalFormatRules();

  rules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenNumberGreaterThanOrEqualTo(75)
    .setBackground('#00B894')
    .setRanges([progressRange])
    .build());
  rules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenNumberBetween(50, 74)
    .setBackground('#FDCB6E')
    .setRanges([progressRange])
    .build());
  rules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenNumberLessThan(50)
    .setBackground('#E17055')
    .setFontColor('white')
    .setRanges([progressRange])
    .build());

  sheet.setConditionalFormatRules(rules);
  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, headers.length);
}

function createHabitsSheet(ss) {
  let sheet = ss.getSheetByName('Habits');
  if (!sheet) {
    sheet = ss.insertSheet('Habits');
  } else {
    sheet.clear();
  }

  const headers = ['Habit', 'Frequency', 'Current Streak', 'Best Streak', 'Done Today', 'Notes'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground('#4ECDC4')
    .setFontColor('white');

  const data = [
    ['Morning routine', 'Daily', 5, 12, true, ''],
    ['Write 500 words', 'Daily', 3, 21, false, ''],
    ['Exercise', 'Daily', 0, 30, false, ''],
    ['Read 30 minutes', 'Daily', 7, 45, true, ''],
    ['Weekly review', 'Weekly', 4, 8, false, ''],
    ['Publish blog post', 'Weekly', 2, 6, false, ''],
    ['Monthly planning', 'Monthly', 1, 3, false, ''],
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);

  // Frequency validation
  const freqRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Daily', 'Weekly', 'Monthly'])
    .build();
  sheet.getRange('B2:B100').setDataValidation(freqRule);

  // Checkbox for Done Today
  sheet.getRange('E2:E100').insertCheckboxes();

  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, headers.length);
}

function createTasksSheet(ss) {
  let sheet = ss.getSheetByName('Tasks');
  if (!sheet) {
    sheet = ss.insertSheet('Tasks');
  } else {
    sheet.clear();
  }

  const headers = ['List', 'Task', 'Priority', 'Due Date', 'Done', 'Notes'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground('#FF6B6B')
    .setFontColor('white');

  const today = new Date().toISOString().split('T')[0];
  const data = [
    ['Today', 'Write blog post on kitchen gadgets', 'High', today, false, ''],
    ['Today', 'Check affiliate dashboard', 'Medium', today, true, ''],
    ['Today', 'Respond to reader comments', 'Low', today, false, ''],
    ['Inbox', 'Research new affiliate products', 'High', '', false, ''],
    ['Inbox', 'Update Pinterest automation', 'Medium', '', false, ''],
    ['Upcoming', 'Quarterly content planning', 'High', '2026-01-05', false, ''],
    ['Upcoming', 'Update SEO for top 10 posts', 'Medium', '2026-01-10', false, ''],
    ['Someday', 'Launch YouTube channel', 'Low', '', false, ''],
    ['Someday', 'Create digital product', 'Medium', '', false, ''],
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);

  // List validation
  const listRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Inbox', 'Today', 'Upcoming', 'Someday'])
    .build();
  sheet.getRange('A2:A100').setDataValidation(listRule);

  // Priority validation
  const priorityRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['High', 'Medium', 'Low'])
    .build();
  sheet.getRange('C2:C100').setDataValidation(priorityRule);

  // Checkbox for Done
  sheet.getRange('E2:E100').insertCheckboxes();

  // Conditional formatting for priority
  const priorityRange = sheet.getRange('C2:C100');
  const rules = [];

  rules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('High')
    .setBackground('#E17055')
    .setFontColor('white')
    .setRanges([priorityRange])
    .build());
  rules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('Medium')
    .setBackground('#FDCB6E')
    .setRanges([priorityRange])
    .build());
  rules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('Low')
    .setBackground('#00B894')
    .setFontColor('white')
    .setRanges([priorityRange])
    .build());

  sheet.setConditionalFormatRules(rules);
  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, headers.length);
}

function createLifeBalanceSheet(ss) {
  let sheet = ss.getSheetByName('Life Balance');
  if (!sheet) {
    sheet = ss.insertSheet('Life Balance');
  } else {
    sheet.clear();
  }

  const headers = ['Life Area', 'Current (1-10)', 'Target (1-10)', 'Gap', 'Action Item'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground('#764ba2')
    .setFontColor('white');

  const data = [
    ['Health & Fitness', 6, 8, '=C2-B2', 'Start morning workouts'],
    ['Career & Business', 7, 9, '=C3-B3', 'Focus on content creation'],
    ['Finances', 5, 8, '=C4-B4', 'Build emergency fund'],
    ['Relationships', 7, 8, '=C5-B5', 'Schedule regular catch-ups'],
    ['Personal Growth', 8, 9, '=C6-B6', 'Continue reading habit'],
    ['Fun & Recreation', 4, 7, '=C7-B7', 'Plan weekend activities'],
    ['Physical Environment', 6, 7, '=C8-B8', 'Declutter office'],
    ['Contribution', 5, 7, '=C9-B9', 'Find volunteer opportunity'],
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);

  // Number validation 1-10
  const numRule = SpreadsheetApp.newDataValidation()
    .requireNumberBetween(1, 10)
    .build();
  sheet.getRange('B2:C100').setDataValidation(numRule);

  // Average row
  sheet.getRange('A11').setValue('AVERAGE').setFontWeight('bold');
  sheet.getRange('B11').setValue('=AVERAGE(B2:B9)').setFontWeight('bold');
  sheet.getRange('C11').setValue('=AVERAGE(C2:C9)').setFontWeight('bold');

  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, headers.length);
}

function createWeeklyReviewSheet(ss) {
  let sheet = ss.getSheetByName('Weekly Review');
  if (!sheet) {
    sheet = ss.insertSheet('Weekly Review');
  } else {
    sheet.clear();
  }

  const headers = ['Week', 'Wins', 'Challenges', 'Lessons Learned', 'Next Week Focus', 'Energy (1-10)', 'Satisfaction (1-10)'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground('#FFE66D');

  sheet.getRange('A2').setValue('2026-W01');

  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, headers.length);
  sheet.setColumnWidths(2, 4, 250);
}

function createJournalSheet(ss) {
  let sheet = ss.getSheetByName('Journal');
  if (!sheet) {
    sheet = ss.insertSheet('Journal');
  } else {
    sheet.clear();
  }

  const headers = ['Date', 'Gratitude (3 things)', "Today's Focus", 'Wins', 'Reflections', 'Mood (1-10)'];
  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground('#00B894')
    .setFontColor('white');

  sheet.getRange('A2').setValue(new Date());

  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, headers.length);
  sheet.setColumnWidths(2, 4, 250);
}

// Add menu for easy access
function onOpen() {
  const ui = SpreadsheetApp.getUi();
  ui.createMenu('Life OS')
    .addItem('Setup Life OS', 'setupLifeOS')
    .addSeparator()
    .addItem('New Journal Entry', 'newJournalEntry')
    .addItem('New Weekly Review', 'newWeeklyReview')
    .addItem('Reset Habits for Today', 'resetHabitsToday')
    .addToUi();
}

function newJournalEntry() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName('Journal');
  const lastRow = sheet.getLastRow() + 1;
  sheet.getRange(lastRow, 1).setValue(new Date());
  sheet.setActiveRange(sheet.getRange(lastRow, 2));
  SpreadsheetApp.getUi().alert('New journal entry created for today!');
}

function newWeeklyReview() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName('Weekly Review');
  const lastRow = sheet.getLastRow() + 1;
  const weekNum = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "YYYY-'W'ww");
  sheet.getRange(lastRow, 1).setValue(weekNum);
  sheet.setActiveRange(sheet.getRange(lastRow, 2));
  SpreadsheetApp.getUi().alert('New weekly review created for ' + weekNum);
}

function resetHabitsToday() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName('Habits');
  const lastRow = sheet.getLastRow();

  // Update streaks before resetting
  for (let i = 2; i <= lastRow; i++) {
    const done = sheet.getRange(i, 5).getValue();
    const currentStreak = sheet.getRange(i, 3).getValue();
    const bestStreak = sheet.getRange(i, 4).getValue();

    if (done) {
      // Increment streak
      const newStreak = currentStreak + 1;
      sheet.getRange(i, 3).setValue(newStreak);
      if (newStreak > bestStreak) {
        sheet.getRange(i, 4).setValue(newStreak);
      }
    } else {
      // Reset streak
      sheet.getRange(i, 3).setValue(0);
    }
  }

  // Reset all checkboxes
  sheet.getRange('E2:E' + lastRow).setValue(false);
  SpreadsheetApp.getUi().alert('Habits reset for new day! Streaks updated.');
}

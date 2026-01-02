/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * LIFE OS - Complete Google Sheets Auto-Build Script
 * ═══════════════════════════════════════════════════════════════════════════════
 *
 * This script automatically builds your complete Life OS spreadsheet with:
 * - 12 interconnected tabs
 * - All formulas and calculations
 * - Conditional formatting
 * - Data validation (dropdowns)
 * - Dashboard with rollups and sparklines
 * - API integration ready (Oura, Shopify)
 *
 * INSTRUCTIONS:
 * 1. Create a new Google Sheet
 * 2. Go to Extensions → Apps Script
 * 3. Delete any existing code
 * 4. Paste this entire script
 * 5. Save (Ctrl+S)
 * 6. Run the function: buildLifeOS()
 * 7. Authorize when prompted
 * 8. Wait for completion (about 30-60 seconds)
 *
 * Author: ShelzyPerkins Life OS
 * Version: 1.0.0
 * ═══════════════════════════════════════════════════════════════════════════════
 */

// ═══════════════════════════════════════════════════════════════════════════════
// CONFIGURATION
// ═══════════════════════════════════════════════════════════════════════════════

const CONFIG = {
  // API Keys - Add your own
  OURA_API_TOKEN: '', // Get from https://cloud.ouraring.com/personal-access-tokens
  SHOPIFY_STORE: '', // your-store.myshopify.com
  SHOPIFY_API_KEY: '',
  SHOPIFY_API_SECRET: '',

  // Optional: Claude/OpenAI API for AI Insights
  CLAUDE_API_KEY: '',
  OPENAI_API_KEY: '',

  // Sheet Colors
  colors: {
    primary: '#FF6B6B',      // Coral Red
    secondary: '#FFE66D',    // Sunny Yellow
    accent: '#4ECDC4',       // Teal
    success: '#2ECC71',      // Green
    warning: '#F39C12',      // Orange
    danger: '#E74C3C',       // Red
    dark: '#2D3436',         // Charcoal
    light: '#F8F9FA',        // Light Gray
    white: '#FFFFFF',
    headerBg: '#2D3436',
    headerText: '#FFFFFF'
  }
};

// ═══════════════════════════════════════════════════════════════════════════════
// MAIN BUILD FUNCTION
// ═══════════════════════════════════════════════════════════════════════════════

function buildLifeOS() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const ui = SpreadsheetApp.getUi();

  ui.alert('🚀 Building Life OS',
    'This will create your complete Life OS system with 12 tabs.\n\n' +
    'This process takes about 30-60 seconds.\n\n' +
    'Click OK to begin.',
    ui.ButtonSet.OK);

  try {
    // Delete default Sheet1 if it exists and is empty
    const defaultSheet = ss.getSheetByName('Sheet1');

    // Build all tabs in order
    buildDashboard(ss);
    buildGoals(ss);
    buildProjects(ss);
    buildHabits(ss);
    buildHealth(ss);
    buildFinances(ss);
    buildRelationships(ss);
    buildLearning(ss);
    buildContent(ss);
    buildIntel(ss);
    buildJournal(ss);
    buildArchive(ss);
    buildSettings(ss);

    // Delete default sheet if we created others
    if (defaultSheet && ss.getSheets().length > 1) {
      try { ss.deleteSheet(defaultSheet); } catch(e) {}
    }

    // Set Dashboard as active
    ss.setActiveSheet(ss.getSheetByName('📊 DASHBOARD'));

    ui.alert('✅ Life OS Built Successfully!',
      'Your Life OS spreadsheet is ready!\n\n' +
      'Next steps:\n' +
      '1. Go to ⚙️ SETTINGS tab to configure APIs\n' +
      '2. Run setupAutomation() to enable daily syncs\n' +
      '3. Start tracking your life!\n\n' +
      'Enjoy your new system!',
      ui.ButtonSet.OK);

  } catch (error) {
    ui.alert('❌ Error', 'An error occurred: ' + error.message, ui.ButtonSet.OK);
    throw error;
  }
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 1: DASHBOARD
// ═══════════════════════════════════════════════════════════════════════════════

function buildDashboard(ss) {
  let sheet = ss.getSheetByName('📊 DASHBOARD');
  if (!sheet) {
    sheet = ss.insertSheet('📊 DASHBOARD');
  } else {
    sheet.clear();
  }

  // Set column widths
  sheet.setColumnWidth(1, 30);   // Spacer
  sheet.setColumnWidth(2, 200);  // Metric name
  sheet.setColumnWidth(3, 120);  // Value
  sheet.setColumnWidth(4, 100);  // Trend
  sheet.setColumnWidth(5, 30);   // Spacer
  sheet.setColumnWidth(6, 200);  // Second column
  sheet.setColumnWidth(7, 120);  // Value
  sheet.setColumnWidth(8, 100);  // Trend

  // Title
  sheet.getRange('B1:H1').merge()
    .setValue('📊 LIFE OS DASHBOARD')
    .setFontSize(24)
    .setFontWeight('bold')
    .setFontColor(CONFIG.colors.dark)
    .setHorizontalAlignment('center');

  sheet.getRange('B2:H2').merge()
    .setValue('=TEXT(TODAY(), "dddd, MMMM d, yyyy")')
    .setFontSize(12)
    .setFontColor('#666666')
    .setHorizontalAlignment('center');

  // Quick Stats Section
  sheet.getRange('B4').setValue('⚡ QUICK STATS').setFontWeight('bold').setFontSize(14);

  const quickStats = [
    ['Active Goals', '=COUNTIF(\'🎯 GOALS\'!E:E,"In Progress")', '📈'],
    ['Open Projects', '=COUNTIF(\'📋 PROJECTS\'!F:F,"Active")', '📊'],
    ['Habits Streak', '=MAX(\'✅ HABITS\'!M:M)', '🔥'],
    ['Health Score', '=AVERAGE(\'💪 HEALTH\'!G:G)', '💚'],
  ];

  let row = 5;
  quickStats.forEach(stat => {
    sheet.getRange(row, 2).setValue(stat[0]).setFontWeight('bold');
    sheet.getRange(row, 3).setFormula(stat[1]).setNumberFormat('0');
    sheet.getRange(row, 4).setValue(stat[2]).setHorizontalAlignment('center');
    row++;
  });

  // Format quick stats section
  sheet.getRange('B5:D8').setBorder(true, true, true, true, false, false, '#DDDDDD', SpreadsheetApp.BorderStyle.SOLID);

  // Second column - More stats
  sheet.getRange('F4').setValue('💰 FINANCIAL SNAPSHOT').setFontWeight('bold').setFontSize(14);

  const financeStats = [
    ['Revenue (MTD)', '=SUMIF(\'💰 FINANCES\'!B:B,">="&DATE(YEAR(TODAY()),MONTH(TODAY()),1),\'💰 FINANCES\'!D:D)', '$#,##0'],
    ['Expenses (MTD)', '=SUMIF(\'💰 FINANCES\'!B:B,">="&DATE(YEAR(TODAY()),MONTH(TODAY()),1),\'💰 FINANCES\'!E:E)', '$#,##0'],
    ['Net Profit', '=G5-G6', '$#,##0'],
    ['Savings Rate', '=IF(G5>0,G7/G5,0)', '0%'],
  ];

  row = 5;
  financeStats.forEach(stat => {
    sheet.getRange(row, 6).setValue(stat[0]).setFontWeight('bold');
    sheet.getRange(row, 7).setFormula(stat[1]).setNumberFormat(stat[2]);
    row++;
  });

  sheet.getRange('F5:G8').setBorder(true, true, true, true, false, false, '#DDDDDD', SpreadsheetApp.BorderStyle.SOLID);

  // Today's Focus Section
  sheet.getRange('B10').setValue('🎯 TODAY\'S FOCUS').setFontWeight('bold').setFontSize(14);
  sheet.getRange('B11:D11').merge().setValue('').setBorder(true, true, true, true, false, false);
  sheet.getRange('B12:D12').merge().setValue('').setBorder(true, true, true, true, false, false);
  sheet.getRange('B13:D13').merge().setValue('').setBorder(true, true, true, true, false, false);

  // Habits Today Section
  sheet.getRange('F10').setValue('✅ HABITS TODAY').setFontWeight('bold').setFontSize(14);

  const habitsList = [
    ['Morning Routine', '☐'],
    ['Exercise', '☐'],
    ['Reading', '☐'],
    ['Meditation', '☐'],
  ];

  row = 11;
  habitsList.forEach(habit => {
    sheet.getRange(row, 6).setValue(habit[0]);
    sheet.getRange(row, 7).setValue(habit[1]).setHorizontalAlignment('center');
    row++;
  });

  // Weekly Progress Section
  sheet.getRange('B16').setValue('📅 WEEKLY PROGRESS').setFontWeight('bold').setFontSize(14);

  const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  days.forEach((day, i) => {
    sheet.getRange(17, 2 + i).setValue(day).setHorizontalAlignment('center').setFontWeight('bold');
  });

  // Sparkline placeholder
  sheet.getRange('B18:H18').merge()
    .setValue('=SPARKLINE({5,7,6,8,7,9,8}, {"charttype","column"; "color","#4ECDC4"})');

  // Recent Activity Section
  sheet.getRange('B20').setValue('📝 RECENT ACTIVITY').setFontWeight('bold').setFontSize(14);
  sheet.getRange('B21:H25').merge()
    .setValue('Activity log will appear here once you start tracking.')
    .setFontColor('#999999')
    .setVerticalAlignment('top');

  // Freeze header
  sheet.setFrozenRows(3);

  // Set tab color
  sheet.setTabColor(CONFIG.colors.primary);
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 2: GOALS
// ═══════════════════════════════════════════════════════════════════════════════

function buildGoals(ss) {
  let sheet = ss.getSheetByName('🎯 GOALS');
  if (!sheet) {
    sheet = ss.insertSheet('🎯 GOALS');
  } else {
    sheet.clear();
  }

  // Headers
  const headers = [
    'ID', 'Goal', 'Category', 'Timeframe', 'Status',
    'Progress %', 'Start Date', 'Target Date', 'Key Results',
    'Notes', 'Priority', 'Last Updated'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  // Set column widths
  const widths = [50, 250, 120, 100, 100, 80, 100, 100, 300, 200, 80, 120];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample data
  const sampleGoals = [
    ['G001', 'Launch new product line', 'Business', 'Q1 2026', 'In Progress', 0.35, '2025-01-01', '2026-03-31', '1. Market research complete\n2. Product designed\n3. Launch campaign ready', '', 'High', '=TODAY()'],
    ['G002', 'Improve fitness level', 'Health', 'Annual', 'In Progress', 0.5, '2025-01-01', '2025-12-31', '1. Exercise 4x/week\n2. Run 5K\n3. Lose 10 lbs', '', 'High', '=TODAY()'],
    ['G003', 'Read 24 books', 'Personal', 'Annual', 'In Progress', 0.25, '2025-01-01', '2025-12-31', '1. 2 books/month\n2. Mix fiction/non-fiction\n3. Take notes', '', 'Medium', '=TODAY()'],
  ];

  if (sampleGoals.length > 0) {
    sheet.getRange(2, 1, sampleGoals.length, sampleGoals[0].length).setValues(sampleGoals);
  }

  // Data Validation - Category
  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Business', 'Health', 'Personal', 'Financial', 'Relationships', 'Learning', 'Creative'], true)
    .build();
  sheet.getRange('C2:C100').setDataValidation(categoryRule);

  // Data Validation - Timeframe
  const timeframeRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Q1 2026', 'Q2 2026', 'Q3 2026', 'Q4 2026', 'Annual', '3-Year', '5-Year'], true)
    .build();
  sheet.getRange('D2:D100').setDataValidation(timeframeRule);

  // Data Validation - Status
  const statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Not Started', 'In Progress', 'On Hold', 'Completed', 'Abandoned'], true)
    .build();
  sheet.getRange('E2:E100').setDataValidation(statusRule);

  // Data Validation - Priority
  const priorityRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['High', 'Medium', 'Low'], true)
    .build();
  sheet.getRange('K2:K100').setDataValidation(priorityRule);

  // Progress bar formatting
  sheet.getRange('F2:F100').setNumberFormat('0%');

  // Conditional Formatting - Status
  const statusRange = sheet.getRange('E2:E100');

  const completedRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('Completed')
    .setBackground('#D5F5E3')
    .setRanges([statusRange])
    .build();

  const inProgressRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('In Progress')
    .setBackground('#FEF9E7')
    .setRanges([statusRange])
    .build();

  const onHoldRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('On Hold')
    .setBackground('#FADBD8')
    .setRanges([statusRange])
    .build();

  sheet.setConditionalFormatRules([completedRule, inProgressRule, onHoldRule]);

  // Freeze header
  sheet.setFrozenRows(1);
  sheet.setTabColor(CONFIG.colors.secondary);
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 3: PROJECTS
// ═══════════════════════════════════════════════════════════════════════════════

function buildProjects(ss) {
  let sheet = ss.getSheetByName('📋 PROJECTS');
  if (!sheet) {
    sheet = ss.insertSheet('📋 PROJECTS');
  } else {
    sheet.clear();
  }

  const headers = [
    'ID', 'Project Name', 'Description', 'Linked Goal', 'Category',
    'Status', 'Priority', 'Start Date', 'Due Date', 'Progress %',
    'Owner', 'Next Action', 'Notes', 'Last Updated'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [50, 200, 250, 100, 100, 100, 80, 100, 100, 80, 100, 200, 200, 120];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample data
  const sampleProjects = [
    ['P001', 'Website Redesign', 'Complete overhaul of company website', 'G001', 'Business', 'Active', 'High', '2025-01-15', '2025-03-15', 0.4, 'Me', 'Finalize mockups', '', '=TODAY()'],
    ['P002', 'Morning Routine System', 'Build consistent morning habits', 'G002', 'Health', 'Active', 'High', '2025-01-01', '2025-02-28', 0.6, 'Me', 'Add meditation', '', '=TODAY()'],
    ['P003', 'Book Club Setup', 'Organize monthly book club', 'G003', 'Personal', 'Planning', 'Medium', '2025-02-01', '2025-03-01', 0.1, 'Me', 'Find members', '', '=TODAY()'],
  ];

  sheet.getRange(2, 1, sampleProjects.length, sampleProjects[0].length).setValues(sampleProjects);

  // Data Validations
  const statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Planning', 'Active', 'On Hold', 'Completed', 'Cancelled'], true)
    .build();
  sheet.getRange('F2:F100').setDataValidation(statusRule);

  const priorityRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Critical', 'High', 'Medium', 'Low'], true)
    .build();
  sheet.getRange('G2:G100').setDataValidation(priorityRule);

  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Business', 'Health', 'Personal', 'Financial', 'Relationships', 'Learning', 'Creative', 'Home'], true)
    .build();
  sheet.getRange('E2:E100').setDataValidation(categoryRule);

  sheet.getRange('J2:J100').setNumberFormat('0%');

  // Conditional formatting for status
  const statusRange = sheet.getRange('F2:F100');

  const activeRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('Active')
    .setBackground('#D4EFDF')
    .setRanges([statusRange])
    .build();

  const planningRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('Planning')
    .setBackground('#EBF5FB')
    .setRanges([statusRange])
    .build();

  const holdRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('On Hold')
    .setBackground('#FCF3CF')
    .setRanges([statusRange])
    .build();

  const completedRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('Completed')
    .setBackground('#D5F5E3')
    .setRanges([statusRange])
    .build();

  sheet.setConditionalFormatRules([activeRule, planningRule, holdRule, completedRule]);

  sheet.setFrozenRows(1);
  sheet.setTabColor(CONFIG.colors.accent);
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 4: HABITS
// ═══════════════════════════════════════════════════════════════════════════════

function buildHabits(ss) {
  let sheet = ss.getSheetByName('✅ HABITS');
  if (!sheet) {
    sheet = ss.insertSheet('✅ HABITS');
  } else {
    sheet.clear();
  }

  // Create habit tracker structure
  const headers = [
    'Habit', 'Category', 'Frequency', 'Time', 'Cue', 'Reward',
    'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
    'Weekly %', 'Current Streak', 'Best Streak', 'Notes'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [150, 100, 80, 80, 150, 150, 40, 40, 40, 40, 40, 40, 40, 80, 80, 80, 200];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample habits
  const sampleHabits = [
    ['Morning Meditation', 'Mindfulness', 'Daily', '6:00 AM', 'After waking', 'Coffee', '✓', '✓', '✓', '', '', '', '', '=COUNTIF(G2:M2,"✓")/7', 3, 14, ''],
    ['Exercise', 'Health', 'Daily', '7:00 AM', 'After meditation', 'Breakfast', '✓', '', '✓', '', '', '', '', '=COUNTIF(G3:M3,"✓")/7', 1, 21, ''],
    ['Read 30 mins', 'Learning', 'Daily', '9:00 PM', 'Before bed', 'Sleep well', '✓', '✓', '', '', '', '', '', '=COUNTIF(G4:M4,"✓")/7', 2, 30, ''],
    ['Journal', 'Mindfulness', 'Daily', '9:30 PM', 'After reading', 'Reflection', '', '✓', '', '', '', '', '', '=COUNTIF(G5:M5,"✓")/7', 1, 7, ''],
    ['Drink 8 glasses water', 'Health', 'Daily', 'All day', 'Hourly alarm', 'Hydration', '✓', '✓', '✓', '', '', '', '', '=COUNTIF(G6:M6,"✓")/7', 3, 45, ''],
  ];

  sheet.getRange(2, 1, sampleHabits.length, sampleHabits[0].length).setValues(sampleHabits);

  // Data Validations
  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Health', 'Mindfulness', 'Learning', 'Productivity', 'Relationships', 'Financial', 'Creative'], true)
    .build();
  sheet.getRange('B2:B50').setDataValidation(categoryRule);

  const frequencyRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Daily', 'Weekdays', 'Weekends', '3x/week', '2x/week', 'Weekly'], true)
    .build();
  sheet.getRange('C2:C50').setDataValidation(frequencyRule);

  // Checkbox-like validation for days
  const checkRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['✓', '✗', '—'], true)
    .build();
  sheet.getRange('G2:M50').setDataValidation(checkRule);

  // Format percentage column
  sheet.getRange('N2:N50').setNumberFormat('0%');

  // Conditional formatting for checkmarks
  const daysRange = sheet.getRange('G2:M50');

  const checkRule1 = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('✓')
    .setBackground('#D5F5E3')
    .setFontColor('#27AE60')
    .setRanges([daysRange])
    .build();

  const xRule = SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo('✗')
    .setBackground('#FADBD8')
    .setFontColor('#E74C3C')
    .setRanges([daysRange])
    .build();

  sheet.setConditionalFormatRules([checkRule1, xRule]);

  // Add week selector at top
  sheet.insertRowBefore(1);
  sheet.getRange('A1').setValue('Week of:');
  sheet.getRange('B1').setValue('=TODAY()-WEEKDAY(TODAY(),2)+1').setNumberFormat('MMM d, yyyy');

  sheet.setFrozenRows(2);
  sheet.setTabColor(CONFIG.colors.success);
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 5: HEALTH
// ═══════════════════════════════════════════════════════════════════════════════

function buildHealth(ss) {
  let sheet = ss.getSheetByName('💪 HEALTH');
  if (!sheet) {
    sheet = ss.insertSheet('💪 HEALTH');
  } else {
    sheet.clear();
  }

  const headers = [
    'Date', 'Sleep Score', 'Sleep Hours', 'HRV', 'Resting HR',
    'Readiness', 'Activity Score', 'Steps', 'Active Calories',
    'Weight', 'Body Fat %', 'Water (glasses)', 'Mood', 'Energy', 'Notes'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [100, 80, 80, 60, 80, 80, 80, 80, 100, 80, 80, 100, 80, 80, 200];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Generate sample data for last 7 days
  const sampleData = [];
  for (let i = 6; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    sampleData.push([
      Utilities.formatDate(date, Session.getScriptTimeZone(), 'yyyy-MM-dd'),
      Math.floor(Math.random() * 20) + 70, // Sleep score
      (Math.random() * 2 + 6).toFixed(1),  // Sleep hours
      Math.floor(Math.random() * 30) + 40, // HRV
      Math.floor(Math.random() * 15) + 55, // Resting HR
      Math.floor(Math.random() * 20) + 70, // Readiness
      Math.floor(Math.random() * 30) + 60, // Activity
      Math.floor(Math.random() * 5000) + 5000, // Steps
      Math.floor(Math.random() * 300) + 200, // Calories
      '', '', // Weight, body fat - manual
      Math.floor(Math.random() * 4) + 5,   // Water
      '',  // Mood
      '',  // Energy
      ''   // Notes
    ]);
  }

  sheet.getRange(2, 1, sampleData.length, sampleData[0].length).setValues(sampleData);

  // Data Validations
  const moodRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['😄 Great', '🙂 Good', '😐 Okay', '😕 Low', '😢 Bad'], true)
    .build();
  sheet.getRange('M2:M500').setDataValidation(moodRule);

  const energyRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['⚡ High', '🔋 Medium', '🪫 Low'], true)
    .build();
  sheet.getRange('N2:N500').setDataValidation(energyRule);

  // Conditional formatting for scores
  const scoreColumns = ['B', 'F', 'G']; // Sleep, Readiness, Activity scores
  scoreColumns.forEach(col => {
    const range = sheet.getRange(col + '2:' + col + '500');

    const highRule = SpreadsheetApp.newConditionalFormatRule()
      .whenNumberGreaterThanOrEqualTo(85)
      .setBackground('#D5F5E3')
      .setRanges([range])
      .build();

    const medRule = SpreadsheetApp.newConditionalFormatRule()
      .whenNumberBetween(70, 84)
      .setBackground('#FCF3CF')
      .setRanges([range])
      .build();

    const lowRule = SpreadsheetApp.newConditionalFormatRule()
      .whenNumberLessThan(70)
      .setBackground('#FADBD8')
      .setRanges([range])
      .build();

    const rules = sheet.getConditionalFormatRules();
    rules.push(highRule, medRule, lowRule);
    sheet.setConditionalFormatRules(rules);
  });

  sheet.setFrozenRows(1);
  sheet.setTabColor('#E74C3C');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 6: FINANCES
// ═══════════════════════════════════════════════════════════════════════════════

function buildFinances(ss) {
  let sheet = ss.getSheetByName('💰 FINANCES');
  if (!sheet) {
    sheet = ss.insertSheet('💰 FINANCES');
  } else {
    sheet.clear();
  }

  const headers = [
    'Transaction ID', 'Date', 'Description', 'Income', 'Expense',
    'Category', 'Account', 'Source', 'Tags', 'Notes', 'Recurring'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [100, 100, 250, 100, 100, 120, 100, 120, 150, 200, 80];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample transactions
  const sampleData = [
    ['TXN001', '=TODAY()-5', 'Shopify Sales', 2500, 0, 'Revenue', 'Business', 'Shopify', 'ecommerce', '', 'No'],
    ['TXN002', '=TODAY()-4', 'Affiliate Commission', 350, 0, 'Revenue', 'Business', 'Amazon', 'affiliate', '', 'No'],
    ['TXN003', '=TODAY()-3', 'Hosting - SiteGround', 0, 29.99, 'Software', 'Business', 'Credit Card', 'recurring', '', 'Yes'],
    ['TXN004', '=TODAY()-2', 'Email Marketing - ConvertKit', 0, 49, 'Software', 'Business', 'Credit Card', 'recurring', '', 'Yes'],
    ['TXN005', '=TODAY()-1', 'Shopify Sales', 1875, 0, 'Revenue', 'Business', 'Shopify', 'ecommerce', '', 'No'],
  ];

  sheet.getRange(2, 1, sampleData.length, sampleData[0].length).setValues(sampleData);

  // Format currency columns
  sheet.getRange('D2:E500').setNumberFormat('$#,##0.00');

  // Data Validations
  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList([
      'Revenue', 'Advertising', 'Software', 'Contractors', 'Office',
      'Travel', 'Education', 'Equipment', 'Fees', 'Other'
    ], true)
    .build();
  sheet.getRange('F2:F500').setDataValidation(categoryRule);

  const accountRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Business', 'Personal', 'Savings', 'Investment'], true)
    .build();
  sheet.getRange('G2:G500').setDataValidation(accountRule);

  const sourceRule = SpreadsheetApp.newDataValidation()
    .requireValueInList([
      'Shopify', 'Amazon', 'PayPal', 'Stripe', 'Bank Transfer',
      'Credit Card', 'Cash', 'Other'
    ], true)
    .build();
  sheet.getRange('H2:H500').setDataValidation(sourceRule);

  const recurringRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Yes', 'No'], true)
    .build();
  sheet.getRange('K2:K500').setDataValidation(recurringRule);

  // Add summary section at bottom
  sheet.getRange('A' + (sampleData.length + 4)).setValue('SUMMARY').setFontWeight('bold');
  sheet.getRange('A' + (sampleData.length + 5)).setValue('Total Income:');
  sheet.getRange('B' + (sampleData.length + 5)).setFormula('=SUM(D:D)').setNumberFormat('$#,##0.00');
  sheet.getRange('A' + (sampleData.length + 6)).setValue('Total Expenses:');
  sheet.getRange('B' + (sampleData.length + 6)).setFormula('=SUM(E:E)').setNumberFormat('$#,##0.00');
  sheet.getRange('A' + (sampleData.length + 7)).setValue('Net:');
  sheet.getRange('B' + (sampleData.length + 7)).setFormula('=B' + (sampleData.length + 5) + '-B' + (sampleData.length + 6)).setNumberFormat('$#,##0.00').setFontWeight('bold');

  sheet.setFrozenRows(1);
  sheet.setTabColor('#F39C12');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 7: RELATIONSHIPS
// ═══════════════════════════════════════════════════════════════════════════════

function buildRelationships(ss) {
  let sheet = ss.getSheetByName('👥 RELATIONSHIPS');
  if (!sheet) {
    sheet = ss.insertSheet('👥 RELATIONSHIPS');
  } else {
    sheet.clear();
  }

  const headers = [
    'Name', 'Relationship', 'Circle', 'Last Contact', 'Contact Frequency',
    'Birthday', 'Email', 'Phone', 'Location', 'How We Met',
    'Interests', 'Notes', 'Next Action', 'Priority'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [150, 100, 80, 100, 120, 100, 180, 120, 120, 150, 200, 200, 150, 80];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample contacts
  const sampleData = [
    ['John Smith', 'Friend', '1', '=TODAY()-3', 'Weekly', '1990-05-15', 'john@email.com', '555-0101', 'New York', 'College', 'Golf, Tech', '', 'Catch up call', 'High'],
    ['Sarah Johnson', 'Colleague', '2', '=TODAY()-10', 'Bi-weekly', '1985-08-22', 'sarah@work.com', '555-0102', 'Remote', 'Work', 'Marketing, Travel', '', '', 'Medium'],
    ['Mom', 'Family', '1', '=TODAY()-1', 'Weekly', '1960-12-01', 'mom@email.com', '555-0103', 'Florida', 'Family', 'Gardening', '', 'Send photos', 'High'],
  ];

  sheet.getRange(2, 1, sampleData.length, sampleData[0].length).setValues(sampleData);

  // Data Validations
  const relationshipRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Family', 'Friend', 'Colleague', 'Mentor', 'Mentee', 'Acquaintance', 'Professional', 'Other'], true)
    .build();
  sheet.getRange('B2:B200').setDataValidation(relationshipRule);

  const circleRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['1', '2', '3', '4', '5'], true)
    .build();
  sheet.getRange('C2:C200').setDataValidation(circleRule);

  const frequencyRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Daily', 'Weekly', 'Bi-weekly', 'Monthly', 'Quarterly', 'Yearly', 'As needed'], true)
    .build();
  sheet.getRange('E2:E200').setDataValidation(frequencyRule);

  const priorityRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['High', 'Medium', 'Low'], true)
    .build();
  sheet.getRange('N2:N200').setDataValidation(priorityRule);

  // Conditional formatting for overdue contacts
  const lastContactRange = sheet.getRange('D2:D200');

  const overdueRule = SpreadsheetApp.newConditionalFormatRule()
    .whenDateBefore(SpreadsheetApp.RelativeDate.PAST_WEEK)
    .setBackground('#FADBD8')
    .setRanges([lastContactRange])
    .build();

  sheet.setConditionalFormatRules([overdueRule]);

  sheet.setFrozenRows(1);
  sheet.setTabColor('#9B59B6');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 8: LEARNING
// ═══════════════════════════════════════════════════════════════════════════════

function buildLearning(ss) {
  let sheet = ss.getSheetByName('📚 LEARNING');
  if (!sheet) {
    sheet = ss.insertSheet('📚 LEARNING');
  } else {
    sheet.clear();
  }

  const headers = [
    'Title', 'Type', 'Category', 'Author/Creator', 'Status',
    'Progress %', 'Start Date', 'End Date', 'Rating', 'Key Takeaways',
    'Notes', 'Review Link', 'Recommend?'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [200, 100, 120, 150, 100, 80, 100, 100, 80, 300, 200, 150, 80];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample learning items
  const sampleData = [
    ['Atomic Habits', 'Book', 'Productivity', 'James Clear', 'Completed', 1, '2025-01-01', '2025-01-15', '⭐⭐⭐⭐⭐', '1% better every day, habit stacking, environment design', 'Life changing book', '', 'Yes'],
    ['Python for Data Science', 'Course', 'Technical', 'DataCamp', 'In Progress', 0.6, '2025-01-10', '', '⭐⭐⭐⭐', 'Pandas, NumPy basics', 'Good for beginners', '', 'Yes'],
    ['The Psychology of Money', 'Book', 'Finance', 'Morgan Housel', 'Reading', 0.3, '2025-01-20', '', '', '', '', '', ''],
  ];

  sheet.getRange(2, 1, sampleData.length, sampleData[0].length).setValues(sampleData);

  // Data Validations
  const typeRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Book', 'Course', 'Podcast', 'Article', 'Video', 'Workshop', 'Certification', 'Other'], true)
    .build();
  sheet.getRange('B2:B200').setDataValidation(typeRule);

  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Productivity', 'Technical', 'Business', 'Finance', 'Health', 'Philosophy', 'Fiction', 'Biography', 'Science', 'Other'], true)
    .build();
  sheet.getRange('C2:C200').setDataValidation(categoryRule);

  const statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['To Start', 'Reading', 'In Progress', 'Paused', 'Completed', 'Abandoned'], true)
    .build();
  sheet.getRange('E2:E200').setDataValidation(statusRule);

  const ratingRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['⭐', '⭐⭐', '⭐⭐⭐', '⭐⭐⭐⭐', '⭐⭐⭐⭐⭐'], true)
    .build();
  sheet.getRange('I2:I200').setDataValidation(ratingRule);

  const recommendRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Yes', 'Maybe', 'No'], true)
    .build();
  sheet.getRange('M2:M200').setDataValidation(recommendRule);

  sheet.getRange('F2:F200').setNumberFormat('0%');

  sheet.setFrozenRows(1);
  sheet.setTabColor('#3498DB');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 9: CONTENT
// ═══════════════════════════════════════════════════════════════════════════════

function buildContent(ss) {
  let sheet = ss.getSheetByName('📝 CONTENT');
  if (!sheet) {
    sheet = ss.insertSheet('📝 CONTENT');
  } else {
    sheet.clear();
  }

  const headers = [
    'ID', 'Title', 'Content Type', 'Platform', 'Category', 'Status',
    'Created Date', 'Publish Date', 'Author', 'URL', 'Engagement',
    'Views', 'Clicks', 'Revenue', 'Notes'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [60, 250, 100, 100, 120, 100, 100, 100, 100, 200, 80, 80, 80, 80, 200];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample content
  const sampleData = [
    ['C001', 'Best Kitchen Gadgets 2025', 'Blog Post', 'Website', 'Kitchen', 'Published', '2025-01-10', '2025-01-12', 'Shelzy', 'https://...', 245, 1250, 89, 125.50, ''],
    ['C002', 'Morning Routine for Success', 'Blog Post', 'Website', 'Lifestyle', 'Draft', '2025-01-18', '', 'Shelzy', '', 0, 0, 0, 0, 'Need images'],
    ['C003', 'Top 10 Beauty Products', 'Social', 'Instagram', 'Beauty', 'Scheduled', '2025-01-15', '2025-01-20', 'Shelzy', '', 0, 0, 0, 0, ''],
  ];

  sheet.getRange(2, 1, sampleData.length, sampleData[0].length).setValues(sampleData);

  // Data Validations
  const typeRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Blog Post', 'Social', 'Email', 'Video', 'Podcast', 'Newsletter', 'Guide', 'Other'], true)
    .build();
  sheet.getRange('C2:C500').setDataValidation(typeRule);

  const platformRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Website', 'Instagram', 'TikTok', 'Pinterest', 'YouTube', 'Email', 'Twitter', 'Facebook', 'Other'], true)
    .build();
  sheet.getRange('D2:D500').setDataValidation(platformRule);

  const categoryRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Kitchen', 'Beauty', 'Tech', 'Fashion', 'Home', 'Fitness', 'Lifestyle', 'Deals', 'Other'], true)
    .build();
  sheet.getRange('E2:E500').setDataValidation(categoryRule);

  const statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Idea', 'Draft', 'Review', 'Scheduled', 'Published', 'Archived'], true)
    .build();
  sheet.getRange('F2:F500').setDataValidation(statusRule);

  sheet.getRange('N2:N500').setNumberFormat('$#,##0.00');

  sheet.setFrozenRows(1);
  sheet.setTabColor('#1ABC9C');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 10: INTEL
// ═══════════════════════════════════════════════════════════════════════════════

function buildIntel(ss) {
  let sheet = ss.getSheetByName('🧠 INTEL');
  if (!sheet) {
    sheet = ss.insertSheet('🧠 INTEL');
  } else {
    sheet.clear();
  }

  // Title
  sheet.getRange('A1:F1').merge()
    .setValue('🧠 INTELLIGENCE & INSIGHTS')
    .setFontSize(20)
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  sheet.getRange('A2:F2').merge()
    .setValue('AI-powered analysis of your Life OS data')
    .setFontSize(12)
    .setFontColor('#666666')
    .setHorizontalAlignment('center');

  // Weekly Analysis Section
  sheet.getRange('A4').setValue('📊 WEEKLY ANALYSIS').setFontWeight('bold').setFontSize(14);
  sheet.getRange('A5:F10').merge()
    .setValue('Weekly analysis will appear here.\n\nTo enable AI insights:\n1. Add your Claude or OpenAI API key in Settings\n2. Run analyzeWeeklyData() function\n3. Insights will be generated automatically every Sunday')
    .setVerticalAlignment('top')
    .setWrap(true)
    .setBorder(true, true, true, true, false, false, '#DDDDDD', SpreadsheetApp.BorderStyle.SOLID);

  // Pattern Detection
  sheet.getRange('A12').setValue('🔍 PATTERN DETECTION').setFontWeight('bold').setFontSize(14);
  sheet.getRange('A13:F18').merge()
    .setValue('Detected patterns:\n\n• [Patterns will appear after data collection]\n• [Correlations between habits and outcomes]\n• [Productivity trends]')
    .setVerticalAlignment('top')
    .setWrap(true)
    .setBorder(true, true, true, true, false, false, '#DDDDDD', SpreadsheetApp.BorderStyle.SOLID);

  // Predictions
  sheet.getRange('A20').setValue('🔮 PREDICTIONS & RECOMMENDATIONS').setFontWeight('bold').setFontSize(14);
  sheet.getRange('A21:F26').merge()
    .setValue('Predictions:\n\n• [Revenue forecast based on trends]\n• [Health score predictions]\n• [Goal completion probability]')
    .setVerticalAlignment('top')
    .setWrap(true)
    .setBorder(true, true, true, true, false, false, '#DDDDDD', SpreadsheetApp.BorderStyle.SOLID);

  // Quick Metrics
  sheet.getRange('A28').setValue('⚡ QUICK METRICS').setFontWeight('bold').setFontSize(14);

  const metrics = [
    ['Metric', 'This Week', 'Last Week', 'Change', 'Trend'],
    ['Productivity Score', '78', '72', '+6', '📈'],
    ['Health Score', '82', '85', '-3', '📉'],
    ['Financial Health', '$2,500', '$1,800', '+$700', '📈'],
    ['Habit Completion', '85%', '78%', '+7%', '📈'],
    ['Learning Hours', '12', '8', '+4', '📈'],
  ];

  sheet.getRange(29, 1, metrics.length, 5).setValues(metrics);
  sheet.getRange(29, 1, 1, 5).setFontWeight('bold').setBackground('#F0F0F0');

  sheet.setColumnWidth(1, 200);
  sheet.setColumnWidth(2, 120);
  sheet.setColumnWidth(3, 120);
  sheet.setColumnWidth(4, 100);
  sheet.setColumnWidth(5, 80);
  sheet.setColumnWidth(6, 200);

  sheet.setTabColor('#8E44AD');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 11: JOURNAL
// ═══════════════════════════════════════════════════════════════════════════════

function buildJournal(ss) {
  let sheet = ss.getSheetByName('📓 JOURNAL');
  if (!sheet) {
    sheet = ss.insertSheet('📓 JOURNAL');
  } else {
    sheet.clear();
  }

  const headers = [
    'Date', 'Day', 'Morning Intention', 'Gratitude (3 things)',
    'Wins Today', 'Challenges', 'Lessons Learned', 'Tomorrow\'s Focus',
    'Mood', 'Energy', 'Score (1-10)'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [100, 80, 200, 250, 200, 200, 200, 200, 100, 100, 80];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Add today's entry template
  const today = new Date();
  const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  const templateRow = [
    Utilities.formatDate(today, Session.getScriptTimeZone(), 'yyyy-MM-dd'),
    dayNames[today.getDay()],
    '', // Morning intention
    '1.\n2.\n3.', // Gratitude
    '', // Wins
    '', // Challenges
    '', // Lessons
    '', // Tomorrow
    '', // Mood
    '', // Energy
    '' // Score
  ];

  sheet.getRange(2, 1, 1, templateRow.length).setValues([templateRow]);

  // Data Validations
  const moodRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['😄 Great', '🙂 Good', '😐 Okay', '😕 Low', '😢 Bad'], true)
    .build();
  sheet.getRange('I2:I500').setDataValidation(moodRule);

  const energyRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['⚡ High', '🔋 Medium', '🪫 Low'], true)
    .build();
  sheet.getRange('J2:J500').setDataValidation(energyRule);

  const scoreRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], true)
    .build();
  sheet.getRange('K2:K500').setDataValidation(scoreRule);

  // Set text wrapping
  sheet.getRange('C2:H500').setWrap(true);

  sheet.setFrozenRows(1);
  sheet.setTabColor('#E67E22');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 12: ARCHIVE
// ═══════════════════════════════════════════════════════════════════════════════

function buildArchive(ss) {
  let sheet = ss.getSheetByName('📦 ARCHIVE');
  if (!sheet) {
    sheet = ss.insertSheet('📦 ARCHIVE');
  } else {
    sheet.clear();
  }

  const headers = [
    'ID', 'Type', 'Name/Title', 'Original Tab', 'Status',
    'Completed Date', 'Outcome', 'Key Learnings', 'Notes'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers])
    .setFontWeight('bold')
    .setBackground(CONFIG.colors.headerBg)
    .setFontColor(CONFIG.colors.headerText)
    .setHorizontalAlignment('center');

  const widths = [80, 100, 250, 120, 100, 120, 200, 300, 200];
  widths.forEach((w, i) => sheet.setColumnWidth(i + 1, w));

  // Sample archived items
  const sampleData = [
    ['G000', 'Goal', 'Complete Website v1', 'GOALS', 'Completed', '2024-12-15', 'Success - launched on time', 'Start smaller, iterate faster', ''],
    ['P000', 'Project', 'Holiday Campaign', 'PROJECTS', 'Completed', '2024-12-20', 'Exceeded revenue target by 25%', 'Email sequences work well', ''],
  ];

  sheet.getRange(2, 1, sampleData.length, sampleData[0].length).setValues(sampleData);

  // Data Validations
  const typeRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Goal', 'Project', 'Habit', 'Learning', 'Content', 'Other'], true)
    .build();
  sheet.getRange('B2:B500').setDataValidation(typeRule);

  const statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Completed', 'Abandoned', 'Deferred', 'Archived'], true)
    .build();
  sheet.getRange('E2:E500').setDataValidation(statusRule);

  sheet.setFrozenRows(1);
  sheet.setTabColor('#95A5A6');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAB 13: SETTINGS
// ═══════════════════════════════════════════════════════════════════════════════

function buildSettings(ss) {
  let sheet = ss.getSheetByName('⚙️ SETTINGS');
  if (!sheet) {
    sheet = ss.insertSheet('⚙️ SETTINGS');
  } else {
    sheet.clear();
  }

  sheet.setColumnWidth(1, 30);
  sheet.setColumnWidth(2, 200);
  sheet.setColumnWidth(3, 350);
  sheet.setColumnWidth(4, 300);

  // Title
  sheet.getRange('B1:D1').merge()
    .setValue('⚙️ LIFE OS SETTINGS')
    .setFontSize(20)
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  // API Configuration Section
  sheet.getRange('B3').setValue('🔑 API CONFIGURATION').setFontWeight('bold').setFontSize(14);

  const apiSettings = [
    ['Setting', 'Value', 'Instructions'],
    ['Oura API Token', '', 'Get from cloud.ouraring.com/personal-access-tokens'],
    ['Shopify Store URL', '', 'Format: your-store.myshopify.com'],
    ['Shopify API Key', '', 'Create private app in Shopify admin'],
    ['Shopify API Secret', '', 'From your Shopify private app'],
    ['Claude API Key', '', 'Optional: For AI insights (api.anthropic.com)'],
    ['OpenAI API Key', '', 'Optional: Alternative for AI insights'],
  ];

  sheet.getRange(4, 2, apiSettings.length, 3).setValues(apiSettings);
  sheet.getRange(4, 2, 1, 3).setFontWeight('bold').setBackground('#F0F0F0');

  // Automation Settings
  sheet.getRange('B12').setValue('⏰ AUTOMATION SETTINGS').setFontWeight('bold').setFontSize(14);

  const automationSettings = [
    ['Setting', 'Value', 'Description'],
    ['Oura Sync Time', '6:00 AM', 'Daily health data sync'],
    ['Shopify Sync Time', '12:00 AM', 'Daily revenue sync'],
    ['Weekly Report Day', 'Sunday', 'When to generate weekly analysis'],
    ['Email Reports', 'No', 'Send weekly summary via email'],
    ['Email Address', '', 'For weekly report delivery'],
  ];

  sheet.getRange(13, 2, automationSettings.length, 3).setValues(automationSettings);
  sheet.getRange(13, 2, 1, 3).setFontWeight('bold').setBackground('#F0F0F0');

  // Quick Actions
  sheet.getRange('B21').setValue('🚀 QUICK ACTIONS').setFontWeight('bold').setFontSize(14);
  sheet.getRange('B22').setValue('To set up automation, run: setupAutomation()');
  sheet.getRange('B23').setValue('To sync Oura data now, run: syncOuraData()');
  sheet.getRange('B24').setValue('To sync Shopify data now, run: syncShopifyData()');
  sheet.getRange('B25').setValue('To generate weekly report, run: generateWeeklyReport()');

  // Version info
  sheet.getRange('B28').setValue('📋 VERSION INFO').setFontWeight('bold').setFontSize(14);
  sheet.getRange('B29').setValue('Life OS Version:');
  sheet.getRange('C29').setValue('1.0.0');
  sheet.getRange('B30').setValue('Last Updated:');
  sheet.getRange('C30').setValue(new Date().toLocaleDateString());

  sheet.setTabColor('#34495E');
}

// ═══════════════════════════════════════════════════════════════════════════════
// API INTEGRATION FUNCTIONS
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Set up automated daily triggers
 */
function setupAutomation() {
  // Delete existing triggers
  const triggers = ScriptApp.getProjectTriggers();
  triggers.forEach(trigger => ScriptApp.deleteTrigger(trigger));

  // Create new triggers
  ScriptApp.newTrigger('syncOuraData')
    .timeBased()
    .atHour(6)
    .everyDays(1)
    .create();

  ScriptApp.newTrigger('syncShopifyData')
    .timeBased()
    .atHour(0)
    .everyDays(1)
    .create();

  ScriptApp.newTrigger('generateWeeklyReport')
    .timeBased()
    .onWeekDay(ScriptApp.WeekDay.SUNDAY)
    .atHour(9)
    .create();

  SpreadsheetApp.getUi().alert('✅ Automation Setup Complete',
    'Daily triggers have been created:\n\n' +
    '• Oura sync: 6:00 AM daily\n' +
    '• Shopify sync: 12:00 AM daily\n' +
    '• Weekly report: Sunday 9:00 AM',
    SpreadsheetApp.getUi().ButtonSet.OK);
}

/**
 * Sync health data from Oura Ring API
 */
function syncOuraData() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const settingsSheet = ss.getSheetByName('⚙️ SETTINGS');
  const healthSheet = ss.getSheetByName('💪 HEALTH');

  // Get API token from settings
  const token = settingsSheet.getRange('C5').getValue();

  if (!token) {
    Logger.log('No Oura API token configured');
    return;
  }

  try {
    // Fetch today's data
    const today = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), 'yyyy-MM-dd');

    // Fetch sleep data
    const sleepUrl = `https://api.ouraring.com/v2/usercollection/daily_sleep?start_date=${today}&end_date=${today}`;
    const sleepResponse = UrlFetchApp.fetch(sleepUrl, {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const sleepData = JSON.parse(sleepResponse.getContentText());

    // Fetch activity data
    const activityUrl = `https://api.ouraring.com/v2/usercollection/daily_activity?start_date=${today}&end_date=${today}`;
    const activityResponse = UrlFetchApp.fetch(activityUrl, {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const activityData = JSON.parse(activityResponse.getContentText());

    // Fetch readiness data
    const readinessUrl = `https://api.ouraring.com/v2/usercollection/daily_readiness?start_date=${today}&end_date=${today}`;
    const readinessResponse = UrlFetchApp.fetch(readinessUrl, {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const readinessData = JSON.parse(readinessResponse.getContentText());

    // Process and add to health sheet
    if (sleepData.data && sleepData.data.length > 0) {
      const sleep = sleepData.data[0];
      const activity = activityData.data && activityData.data.length > 0 ? activityData.data[0] : {};
      const readiness = readinessData.data && readinessData.data.length > 0 ? readinessData.data[0] : {};

      // Find or create today's row
      const lastRow = healthSheet.getLastRow();
      let targetRow = lastRow + 1;

      // Check if today already exists
      const dates = healthSheet.getRange('A2:A' + lastRow).getValues();
      for (let i = 0; i < dates.length; i++) {
        if (Utilities.formatDate(new Date(dates[i][0]), Session.getScriptTimeZone(), 'yyyy-MM-dd') === today) {
          targetRow = i + 2;
          break;
        }
      }

      // Update data
      healthSheet.getRange(targetRow, 1).setValue(today);
      healthSheet.getRange(targetRow, 2).setValue(sleep.score || '');
      healthSheet.getRange(targetRow, 3).setValue(sleep.contributors?.total_sleep ? sleep.contributors.total_sleep / 3600 : '');
      healthSheet.getRange(targetRow, 4).setValue(readiness.contributors?.hrv_balance || '');
      healthSheet.getRange(targetRow, 5).setValue(readiness.contributors?.resting_heart_rate || '');
      healthSheet.getRange(targetRow, 6).setValue(readiness.score || '');
      healthSheet.getRange(targetRow, 7).setValue(activity.score || '');
      healthSheet.getRange(targetRow, 8).setValue(activity.steps || '');
      healthSheet.getRange(targetRow, 9).setValue(activity.active_calories || '');

      Logger.log('Oura data synced successfully for ' + today);
    }

  } catch (error) {
    Logger.log('Error syncing Oura data: ' + error.message);
  }
}

/**
 * Sync revenue data from Shopify
 */
function syncShopifyData() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const settingsSheet = ss.getSheetByName('⚙️ SETTINGS');
  const financeSheet = ss.getSheetByName('💰 FINANCES');

  const storeUrl = settingsSheet.getRange('C6').getValue();
  const apiKey = settingsSheet.getRange('C7').getValue();
  const apiSecret = settingsSheet.getRange('C8').getValue();

  if (!storeUrl || !apiKey || !apiSecret) {
    Logger.log('Shopify credentials not configured');
    return;
  }

  try {
    // Calculate yesterday's date range
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const startDate = Utilities.formatDate(yesterday, Session.getScriptTimeZone(), "yyyy-MM-dd'T'00:00:00");
    const endDate = Utilities.formatDate(yesterday, Session.getScriptTimeZone(), "yyyy-MM-dd'T'23:59:59");

    // Fetch orders
    const url = `https://${apiKey}:${apiSecret}@${storeUrl}/admin/api/2024-01/orders.json?status=any&created_at_min=${startDate}&created_at_max=${endDate}`;

    const response = UrlFetchApp.fetch(url);
    const data = JSON.parse(response.getContentText());

    if (data.orders && data.orders.length > 0) {
      // Calculate daily totals
      let totalRevenue = 0;
      data.orders.forEach(order => {
        totalRevenue += parseFloat(order.total_price || 0);
      });

      // Add to finance sheet
      const lastRow = financeSheet.getLastRow() + 1;
      const dateStr = Utilities.formatDate(yesterday, Session.getScriptTimeZone(), 'yyyy-MM-dd');

      financeSheet.getRange(lastRow, 1).setValue('SHOP-' + dateStr);
      financeSheet.getRange(lastRow, 2).setValue(yesterday);
      financeSheet.getRange(lastRow, 3).setValue('Shopify Sales (' + data.orders.length + ' orders)');
      financeSheet.getRange(lastRow, 4).setValue(totalRevenue);
      financeSheet.getRange(lastRow, 5).setValue(0);
      financeSheet.getRange(lastRow, 6).setValue('Revenue');
      financeSheet.getRange(lastRow, 7).setValue('Business');
      financeSheet.getRange(lastRow, 8).setValue('Shopify');
      financeSheet.getRange(lastRow, 9).setValue('ecommerce,auto-sync');

      Logger.log('Shopify data synced: $' + totalRevenue + ' from ' + data.orders.length + ' orders');
    }

  } catch (error) {
    Logger.log('Error syncing Shopify data: ' + error.message);
  }
}

/**
 * Generate weekly analysis report
 */
function generateWeeklyReport() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const intelSheet = ss.getSheetByName('🧠 INTEL');

  // Gather metrics from all sheets
  const metrics = gatherWeeklyMetrics(ss);

  // Format the report
  const report = formatWeeklyReport(metrics);

  // Update Intel sheet
  intelSheet.getRange('A5:F10').setValue(report);

  // Optional: Use AI to generate insights
  const settingsSheet = ss.getSheetByName('⚙️ SETTINGS');
  const claudeKey = settingsSheet.getRange('C9').getValue();
  const openaiKey = settingsSheet.getRange('C10').getValue();

  if (claudeKey || openaiKey) {
    const aiInsights = generateAIInsights(metrics, claudeKey, openaiKey);
    intelSheet.getRange('A13:F18').setValue(aiInsights);
  }

  Logger.log('Weekly report generated');
}

function gatherWeeklyMetrics(ss) {
  const now = new Date();
  const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

  const metrics = {
    goals: {
      total: 0,
      completed: 0,
      inProgress: 0
    },
    projects: {
      active: 0,
      completed: 0
    },
    habits: {
      avgCompletion: 0
    },
    health: {
      avgSleep: 0,
      avgReadiness: 0,
      avgSteps: 0
    },
    finance: {
      income: 0,
      expenses: 0
    }
  };

  // Goals metrics
  const goalsSheet = ss.getSheetByName('🎯 GOALS');
  if (goalsSheet) {
    const goalsData = goalsSheet.getDataRange().getValues();
    for (let i = 1; i < goalsData.length; i++) {
      if (goalsData[i][0]) {
        metrics.goals.total++;
        if (goalsData[i][4] === 'Completed') metrics.goals.completed++;
        if (goalsData[i][4] === 'In Progress') metrics.goals.inProgress++;
      }
    }
  }

  // Finance metrics
  const financeSheet = ss.getSheetByName('💰 FINANCES');
  if (financeSheet) {
    const financeData = financeSheet.getDataRange().getValues();
    for (let i = 1; i < financeData.length; i++) {
      const date = new Date(financeData[i][1]);
      if (date >= weekAgo && date <= now) {
        metrics.finance.income += parseFloat(financeData[i][3]) || 0;
        metrics.finance.expenses += parseFloat(financeData[i][4]) || 0;
      }
    }
  }

  // Health metrics
  const healthSheet = ss.getSheetByName('💪 HEALTH');
  if (healthSheet) {
    const healthData = healthSheet.getDataRange().getValues();
    let sleepTotal = 0, readinessTotal = 0, stepsTotal = 0, count = 0;
    for (let i = 1; i < healthData.length; i++) {
      const date = new Date(healthData[i][0]);
      if (date >= weekAgo && date <= now) {
        sleepTotal += parseFloat(healthData[i][1]) || 0;
        readinessTotal += parseFloat(healthData[i][5]) || 0;
        stepsTotal += parseFloat(healthData[i][7]) || 0;
        count++;
      }
    }
    if (count > 0) {
      metrics.health.avgSleep = Math.round(sleepTotal / count);
      metrics.health.avgReadiness = Math.round(readinessTotal / count);
      metrics.health.avgSteps = Math.round(stepsTotal / count);
    }
  }

  return metrics;
}

function formatWeeklyReport(metrics) {
  const weekStart = new Date();
  weekStart.setDate(weekStart.getDate() - 7);

  return `📊 WEEKLY REPORT: ${Utilities.formatDate(weekStart, Session.getScriptTimeZone(), 'MMM d')} - ${Utilities.formatDate(new Date(), Session.getScriptTimeZone(), 'MMM d, yyyy')}

GOALS: ${metrics.goals.completed}/${metrics.goals.total} completed | ${metrics.goals.inProgress} in progress

HEALTH AVERAGES:
• Sleep Score: ${metrics.health.avgSleep}
• Readiness: ${metrics.health.avgReadiness}
• Daily Steps: ${metrics.health.avgSteps.toLocaleString()}

FINANCES:
• Income: $${metrics.finance.income.toLocaleString()}
• Expenses: $${metrics.finance.expenses.toLocaleString()}
• Net: $${(metrics.finance.income - metrics.finance.expenses).toLocaleString()}`;
}

function generateAIInsights(metrics, claudeKey, openaiKey) {
  const prompt = `Analyze these weekly life metrics and provide 3-5 actionable insights:

${JSON.stringify(metrics, null, 2)}

Provide insights about patterns, recommendations for improvement, and areas of strength. Keep it concise.`;

  try {
    if (claudeKey) {
      // Use Claude API
      const response = UrlFetchApp.fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': claudeKey,
          'anthropic-version': '2023-06-01'
        },
        payload: JSON.stringify({
          model: 'claude-3-haiku-20240307',
          max_tokens: 500,
          messages: [{ role: 'user', content: prompt }]
        })
      });

      const data = JSON.parse(response.getContentText());
      return data.content[0].text;

    } else if (openaiKey) {
      // Use OpenAI API
      const response = UrlFetchApp.fetch('https://api.openai.com/v1/chat/completions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + openaiKey
        },
        payload: JSON.stringify({
          model: 'gpt-3.5-turbo',
          messages: [{ role: 'user', content: prompt }],
          max_tokens: 500
        })
      });

      const data = JSON.parse(response.getContentText());
      return data.choices[0].message.content;
    }
  } catch (error) {
    return 'AI insights unavailable: ' + error.message;
  }

  return 'No AI API configured. Add Claude or OpenAI key in Settings.';
}

// ═══════════════════════════════════════════════════════════════════════════════
// UTILITY FUNCTIONS
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Create custom menu
 */
function onOpen() {
  const ui = SpreadsheetApp.getUi();
  ui.createMenu('🚀 Life OS')
    .addItem('📊 Refresh Dashboard', 'refreshDashboard')
    .addSeparator()
    .addItem('🔄 Sync Oura Data', 'syncOuraData')
    .addItem('🛒 Sync Shopify Data', 'syncShopifyData')
    .addItem('📈 Generate Weekly Report', 'generateWeeklyReport')
    .addSeparator()
    .addItem('⚙️ Setup Automation', 'setupAutomation')
    .addItem('🔧 Rebuild All Tabs', 'buildLifeOS')
    .addToUi();
}

/**
 * Refresh dashboard calculations
 */
function refreshDashboard() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const dashboard = ss.getSheetByName('📊 DASHBOARD');

  // Force recalculation by touching a cell
  const range = dashboard.getRange('B2');
  const value = range.getValue();
  range.setValue(value);

  SpreadsheetApp.flush();

  SpreadsheetApp.getUi().alert('Dashboard refreshed!');
}

/**
 * Quick add functions for mobile/shortcuts
 */
function quickAddHabit() {
  const ui = SpreadsheetApp.getUi();
  const response = ui.prompt('Add New Habit', 'Enter habit name:', ui.ButtonSet.OK_CANCEL);

  if (response.getSelectedButton() === ui.Button.OK) {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName('✅ HABITS');
    const lastRow = sheet.getLastRow() + 1;
    sheet.getRange(lastRow, 1).setValue(response.getResponseText());
    ui.alert('Habit added!');
  }
}

function quickAddExpense() {
  const ui = SpreadsheetApp.getUi();
  const desc = ui.prompt('Quick Expense', 'Description:', ui.ButtonSet.OK_CANCEL);

  if (desc.getSelectedButton() === ui.Button.OK) {
    const amount = ui.prompt('Amount', 'Enter amount ($):', ui.ButtonSet.OK_CANCEL);

    if (amount.getSelectedButton() === ui.Button.OK) {
      const ss = SpreadsheetApp.getActiveSpreadsheet();
      const sheet = ss.getSheetByName('💰 FINANCES');
      const lastRow = sheet.getLastRow() + 1;

      sheet.getRange(lastRow, 1).setValue('TXN' + lastRow);
      sheet.getRange(lastRow, 2).setValue(new Date());
      sheet.getRange(lastRow, 3).setValue(desc.getResponseText());
      sheet.getRange(lastRow, 5).setValue(parseFloat(amount.getResponseText()));

      ui.alert('Expense added!');
    }
  }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Security Lab Portal</title>
    <link rel="stylesheet" href="/assets/css/portswigger.css">
</head>
<body style="background-color: #f8fafc;">

    <div class="store-container" style="max-width: 900px; margin: 50px auto;">
        <header class="store-header" style="border-bottom: 3px solid #ff6600; padding-bottom: 16px;">
            <a href="/" class="store-brand" style="font-size: 30px;">🛡️ SQL Injection Security Lab Portal</a>
        </header>

        <p style="color: #64748b; font-size: 16px; margin-bottom: 30px;">Select a practical web security lab environment below to demonstrate attack vectors and mitigation strategies:</p>

        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Lab 1 Card -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; gap: 20px;">
                <div>
                    <span style="background: #ef4444; color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; letter-spacing: 0.5px;">LAB 1</span>
                    <h2 style="margin: 10px 0 8px 0; color: #0f172a; font-size: 20px;">SQL Injection UNION Attack (Single Column Output)</h2>
                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">
                        Demonstrates extracting database credentials through a product category filter where only a single string column is rendered on screen. Requires string concatenation techniques (<code>CONCAT</code>) to retrieve sensitive account data.
                    </p>
                </div>
                <a href="/lab1-union-sqli/" class="btn-view" style="white-space: nowrap; padding: 14px 24px; font-size: 15px; font-weight: bold; text-decoration: none;">Launch Lab 1 &rarr;</a>
            </div>

            <!-- Lab 2 Card -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; gap: 20px;">
                <div>
                    <span style="background: #3b82f6; color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; letter-spacing: 0.5px;">LAB 2</span>
                    <h2 style="margin: 10px 0 8px 0; color: #0f172a; font-size: 20px;">Time-based Blind SQL Injection</h2>
                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">
                        Demonstrates asynchronous blind SQL injection in a HTTP Cookie tracking header (<code>TrackingId</code>). The application returns no visible query results or SQL errors on screen. Injects conditional <code>SLEEP()</code> delays to exfiltrate administrator passwords bit-by-bit.
                    </p>
                </div>
                <a href="/lab2-time-blind-sqli/" class="btn-view" style="white-space: nowrap; padding: 14px 24px; font-size: 15px; font-weight: bold; text-decoration: none;">Launch Lab 2 &rarr;</a>
            </div>

        </div>
    </div>

</body>
</html>

@echo off
REM RSS Feed Aggregator - Windows Batch File
REM Run this file to fetch and publish articles from RSS feeds

echo Starting RSS Feed Aggregator...
echo.

cd /d "%~dp0"
C:\xampp\php\php.exe rss-feed-aggregator.php

echo.
echo RSS Feed Aggregator completed!
pause

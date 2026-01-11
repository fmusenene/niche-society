@echo off
echo Initializing Git push to GitHub...
echo.

cd /d "C:\xampp\htdocs\niche-society-main"

echo Checking git status...
git status

echo.
echo Adding all files...
git add .

echo.
echo Committing changes...
git commit -m "Update contact form email, social media links, CEO section design, and mobile ISO banner styling"

echo.
echo Setting remote URL...
git remote set-url origin https://github.com/mubahood/niche-society.git

echo.
echo Checking current branch...
git branch

echo.
echo Pushing to GitHub...
git push -u origin main

if %errorlevel% neq 0 (
    echo.
    echo Trying with master branch instead...
    git push -u origin master
)

echo.
echo Done!
pause

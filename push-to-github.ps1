# PowerShell script to push changes to GitHub
Write-Host "Initializing Git push to GitHub..." -ForegroundColor Green
Write-Host ""

Set-Location "C:\xampp\htdocs\niche-society-main"

Write-Host "Checking git status..." -ForegroundColor Yellow
git status

Write-Host ""
Write-Host "Adding all files..." -ForegroundColor Yellow
git add .

Write-Host ""
Write-Host "Committing changes..." -ForegroundColor Yellow
git commit -m "Update contact form email, social media links, CEO section design, and mobile ISO banner styling"

Write-Host ""
Write-Host "Setting remote URL..." -ForegroundColor Yellow
git remote set-url origin https://github.com/mubahood/niche-society.git

Write-Host ""
Write-Host "Checking current branch..." -ForegroundColor Yellow
git branch

Write-Host ""
Write-Host "Pushing to GitHub..." -ForegroundColor Yellow
git push -u origin main

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "Trying with master branch instead..." -ForegroundColor Yellow
    git push -u origin master
}

Write-Host ""
Write-Host "Done!" -ForegroundColor Green
Read-Host "Press Enter to continue"

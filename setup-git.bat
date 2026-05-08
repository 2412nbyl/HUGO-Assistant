@echo off
:: HUGO-Assistant — First-time GitHub Setup
:: Run this ONCE to initialize git and push all code to GitHub

echo ========================================
echo   HUGO-Assistant — GitHub Setup
echo ========================================
echo.

set REPO_URL=https://github.com/NN242224/HUGO-Assistant.git

:: Init git
git init
echo [OK] Git initialized

:: Set default branch to main
git branch -M main

:: Add remote origin
git remote remove origin 2>nul
git remote add origin %REPO_URL%
echo [OK] Remote set to %REPO_URL%

:: Stage all files
git add .
echo [OK] Files staged

:: Commit
git commit -m "Initial commit: HUGO-Assistant system"
echo [OK] Initial commit done

:: Push
echo.
echo Pushing to GitHub...
git push -u origin main
echo.
echo ========================================
echo   DONE! Check https://github.com/NN242224/HUGO-Assistant
echo ========================================
pause

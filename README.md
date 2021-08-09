<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## About the test

## Test Requirements

1. ✅ Private git repo
2. Once finished share it with users: `SamuelNorbury` and `juampi92`
3. ✅  Provide a docker-compose file or use sail
4. Provide a README with: 
   - How to use instruction
   - External dependencies use justification
5. ✅  Create a CLI interactive
6. ✅  The command name should be: `qanda:interactive`
7. ✅  Once started the main screen should list the following:
   ```
   1. Create a question 
   2. List all questions
   3. Pratice
   4. Stats
   5. Reset
   6. Exit
   ```
8. ✅  About `Create a question`:
   - ✅  prompt to give a question and the only answer to that question.
   - ✅  Both the question and the answer should be stored in the database.
    
9. ✅  About `List all questions`:
    - shows table listing all the created questions with the correct answer.

10. ✅  About `Pratice`:
    - ✅  Shows a table listing all questions, and their practice status for each question:
      Not answered, Correct, Incorrect.
    - ✅  Table footer, should present the % of completion (all questions vs correctly answered).
    - ✅  The user should select the desired question.
    - ✅  Once flagged as correct the question can't be selected.
    - ✅  Upon answering, we store it and print correct/incorrect.
    - ✅  After store the answer and display the result the user should go back to `Pratice` screen

11. ✅  About `Stats`: 
    - ✅  Shows The total amount of questions.
    - ✅  % of questions that have an answer.
    - ✅  % of questions that have a correct answer.

12. ✅  About `Reset`:
    - ✅  erase all practice progress and allow a fresh start.
    
13. General Notes:
    - ✅  Perform any validations you deem necessary.
    - ✅  Persisted in a SQL database.
    - ✅  Allow multiple users.
    - Clean code solutions are better
    - ✅  Test!!

## Dependencies

- No dependencies, Only Laravel features

## How to run

# Video Presentation


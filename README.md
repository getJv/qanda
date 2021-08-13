<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Solution description

To this solution I created a Laravel commmand the uses the Single Responsabilite Pattern to handle all the Screen Calls. 

## Dependencies

- from PHP: No dependencies . Only Laravel already in features. 
- from OS PHP: Docker and Docker compose
- Developed using Windows 10 with WSL2 which Ubuntu OS based.

## How to run

1. Checkout the project from git: `git clone https://github.com/getJv/qanda.git`
2. Go into project folder. `cd qanda`
3. execute one of the following action: 
   * `sh qanda-bigbang.sh` - to execute all steps in once
   *  To execute directly on you favorite terminal:
   ```
     docker-compose up -d && docker exec --user 1000 backend composer install && docker exec backend php artisan migrate --seed && docker exec -it backend php artisan qanda:interactive  
   ```
   _Please, remember to waiting some seconds to database container started._
4. Enjoy!

### Useful commands:
* `docker exec -it backend bash` To go into the container
* Once inside you can run:
   * `php artisan test --filter=QandaTest` to check the tests
   * `php artisan qanda:interactive` to execute the app in a clean window
   * `php artisan qanda:interactive --sequential` to execute in a verbose mode. 
   * `php artisan migrate:fresh --seed` to reset and reload the whole database


### Important notes
 
* If something goes wrong, you can open the file `qanda-bigbang.sh` and execute each command one-by-one. Sometimes the OS play with us... :(
* I'm using my own php docker image... sometimes you can get some xdebug messages at console... please ignore them.
* The .env file was commit for time-saver purposes. (Don't do that in home people :D )
* If thing going awful, please free feel to call/text me.

## Test Requirements checklist


1. ✅  Provide a docker-compose file or use sail
2. ✅  Provide a README with: 
   - ✅  How to use instruction
   - ✅  External dependencies use justification
3. ✅  Create a CLI interactive
4. ✅  The command name should be: `qanda:interactive`
5. ✅  Once started the main screen should list the following:
   ```
   1. Create a question 
   2. List all questions
   3. Pratice
   4. Stats
   5. Reset
   6. Exit
   ```
6. ✅  About `Create a question`:
   - ✅  prompt to give a question and the only answer to that question.
   - ✅  Both the question and the answer should be stored in the database.
    
7. ✅  About `List all questions`:
    - shows table listing all the created questions with the correct answer.

8. ✅  About `Pratice`:
    - ✅  Shows a table listing all questions, and their practice status for each question:
      Not answered, Correct, Incorrect.
    - ✅  Table footer, should present the % of completion (all questions vs correctly answered).
    - ✅  The user should select the desired question.
    - ✅  Once flagged as correct the question can't be selected.
    - ✅  Upon answering, we store it and print correct/incorrect.
    - ✅  After store the answer and display the result the user should go back to `Pratice` screen

9. ✅  About `Stats`: 
    - ✅  Shows The total amount of questions.
    - ✅  % of questions that have an answer.
    - ✅  % of questions that have a correct answer.

10. ✅  About `Reset`:
    - ✅  erase all practice progress and allow a fresh start.
    
11. General Notes:
    - ✅  Perform any validations you deem necessary.
    - ✅  Persisted in a SQL database.
    - ✅  Allow multiple users.
    - ✅  Clean code solutions are better
    - ✅  Test!!






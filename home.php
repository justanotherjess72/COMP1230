<?php


/**************************************** GROUP NOTES ***************************************/
/*
 *  YOU WILL FIND COMMENTS THROUGHOUT <-- these are for you as much as me!
 *
 *  These functions are not promised to work right away and will definitely need collaboration
 *  There are going to be variables that need their names changed
 *
 *  I used MYSQLI however we can absolutely use PDO;
 *
 *  To my understanding whichever one we choose is a preference on the development end:
 *
 *  MYSQLI --> is designed specifically for databases that are MYSQL
 *  PDO --> working with multiple databases like MYSQL or MYSQLite etc.
 *  ^----- cleaner interface and easier to work with, just let me know if this is what we are going to go with!
 *
 *  (im not sure which type of database we are using within our group, but I presume its MYSQL, I could be wrong)
 *  BOTH will help with mysql injection
 *
 *  You can find an example of the PDO being used in searchPathways.php
 *  ^--- this was adapted from the examples we were given in our lab
 *
 *  I am unsure if their expectation is to have PDO used throughout the project.
 *  If so this is a simple adjustment.
 *
 *
 * MISSING FUNCTIONALITIES:
 *  1. Sharing
 *  2. Cloning
 *
 * // WARNING
 *  if you see this and think it is a mess --> I am so sorry, I know, I had been working on these in the background
 *  and am well aware that they could use some work
 *
 *  there are absolutely going to be syntax errors, you will potentially find things missing along the way
 *
 *
 * [ FEEL FREEEEEEEE TO USE THIS home.php FOR CODE, AND BY ALL MEANS DELETE THESE COMMENTS]
 *
 *
 *
 * */
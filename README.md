SimpleSignUp
============

A simple PHP web app for signing up experiment participants.


Who is SimpleSignUp for?
------------------------

You're running an experiment and you need to sign up participants. You don't want to be inundated with emails and have to deal with sorting out the schedule manually. It would also be nice if you could exclude certain people who've (a) already done the experiment or (b) already done a similar experiment. You may also want to exclude people who've taken part in a experiment conducted by a colleague in your lab. SimpleSignUp solves these kinds of problems.

Another neat feature is that you can define how many people you need per timeslot, which is great for multi-person experiments. The calendar display will show which slots are available and recommend slots that are partially full.

SimpleSignUp's data is stored in plain text files, so it's easy to take the data and do something else with it. It is an open source project and you're encouraged to fork the code and modify to your taste.


Requirements
------------

You'll need a server capable of running PHP scripts. You'll need suitable permissions to allow PHP to write to files on the server. This code has only been tested on Apache 2.4.9 with PHP 5.5.14. Presumably, any recent versions of Apache/PHP should have little difficulty running it.


Important security matter
-------------------------

To protect the personal details of your participants, you must secure SimpleSignUp's /data/ directory from unauthorized access. To do this, set up a .htaccess file in this directory. If you do not do this, anyone could grab your participants' details, which would be bad. In the future, the plan is to automate this process and allow you to give access to your colleagues.


Limitations
-----------

SimpleSignUp is new and has not been tested extensively. Various new features are on the books. The priority feature is an admin area so that you can add, delete, and edit your experiments more easily without having to go mess about with the raw data files.

Check the issues for this repo to get an idea of the other outstanding features planned for SimpleSignUp.


Data format
-----------

All data is stored in plain text files to keep things simple. The format of these files is documented below:

### data/experiments

This provides a look-up table so that you can establish the owner of a given experiment ID. Each line should contain something like:

> *MY_CHOSEN_EXPERIMENT_ID = {MY_USERNAME}*

### data/users

This contains the details of the users (i.e. experimentors). Each line should contain something like:

> *MY_USERNAME = { name = [MY_NAME], email = [MY_EMAIL_ADDRESS], phone = [MY_PHONE_NUMBER] }*

### data/user_data/USERNAME/EXPERIMENT_ID

These files contain the parameters that define an experiment. Currently, the following parameters should be set:

- name = {The name of the experiment}

- status = {open}

- description = {Description of the experiment}

- location = {Where the experiment will take place}

- requirements = {Right handed; Native speaker; No glasses}

- exclusions = {experiment1; experiment2; experiment3}

- max_participants = {100}

- per_slot = {2}

- number_of_slots = {50}

- slot_time = {60}

- calendar = {2014/11/01: 09:00 = 1, 10:00 = 2; 2014/11/02: 15:00 = 3}

- exclusion_emails = {i_am_excluded@thatsucks.com}

- slot1 = {Mary Jones, mary@jones.com, 07000222333; John Smith, john@smith.com, 07000444555}

- slot2 = {}

- slot3 = {}

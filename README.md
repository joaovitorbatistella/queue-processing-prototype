# Queue Processing Prototype

## Get started
### Copy .sh file from cron folder in your app to /var/queue_processing_prototype
 * look like this `/var/queue_processing_prototype/queue_script.sh`
 
### Check the path pointing in your `queue_script.sh` 
 * Ecample: `php /var/www/queue-processing/cron/cron.php >> /tmp/arq1`


### Add this line to user crontab:
```sh
 * * * * * /bin/sh /var/queue_processing_prototype/queue_script.sh  >> /tmp/arq1
```
 * To edit crontab, run: `crontab -e -u $USER`

### Should look like this:
```sh
# DO NOT EDIT THIS FILE - edit the master and reinstall.
# (/tmp/crontab.6uiocf/crontab installed on Sun Jul  9 23:27:59 2023)
# (Cron version -- $Id: crontab.c,v 2.13 1994/01/17 03:20:37 vixie Exp $)
# Edit this file to introduce tasks to be run by cron.
# 
# Each task to run has to be defined through a single line
# indicating with different fields when the task will be run
# and what command to run for the task
# 
# To define the time you can provide concrete values for
# minute (m), hour (h), day of month (dom), month (mon),
# and day of week (dow) or use '*' in these fields (for 'any').
# 
# Notice that tasks will be started based on the cron's system
# daemon's notion of time and timezones.
# 
# Output of the crontab jobs (including errors) is sent through
# email to the user the crontab file belongs to (unless redirected).
# 
# For example, you can run a backup of all your user accounts
# at 5 a.m every week with:
# 0 5 * * 1 tar -zcf /var/backups/home.tgz /home/
# 
# For more information see the manual pages of crontab(5) and cron(8)
# 
# m h  dom mon dow   command

SHELL=/bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

* * * * * /bin/sh /var/queue_processing_prototype/queue_script.sh  >> /tmp/arq1
# * * * * * echo "ony for test" >> /tmp/arq1
```

Video pt1: https://loom.com/share/b8370e55ef2343808d8395a858359c18
Video pt2: https://loom.com/share/25c97b968d3149bfaf6e21fce5f19562

### By: João Vítor Batistella
## Up to date
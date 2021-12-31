#!/bin/bash
# WALTR 1.1.0 - Link Scraper With MySQL DB
# If you're using the WALTR.php file, you can substitute
# this code in or wait for the updated php file
# coming in 2022, Happy Holidays!
#
# Basics:
# chmod +x WALTR.sh
#
# Useage: ./WALTR.sh -u some.com (destination url)
# Do Not Use: http:// https:// ftp:// www.
#
# Suggestions: proxychains, tor, remote, etc.
#
# Edit MySQL DB Server Config..
DB_HOST='localhost'
DB_USER='root'
DB_PASS=''
DB_NAME='WALTR'
# End Config

# WALTR
TIMESTAMP=$( date )
while getopts u: flag
 do
case "${flag}" in
        u) TargetURL=${OPTARG};;
    esac
 done
    # Passing The Password Is Gonna Throw Some Warning Flags.
    mysql -u$DB_USER -p$DB_PASS -e "CREATE DATABASE $DB_NAME;"
    mysql -u$DB_USER -p$DB_PASS -D$DB_NAME -e "CREATE TABLE URLS (URL VARCHAR(254), LINKS VARCHAR(254), TIMESTAMP VARCHAR(50));"
    # Collect Only The Links From The Target URL.
    wget -q $TargetURL -O - | \
        tr "\t\r\n'" '   "' | \
        grep -i -o '<a[^>]\+href[ ]*=[ \t]*"\(ht\|f\)tps\?:[^"]\+"' | \
        sed -e 's/^.*"\([^"]\+\)".*$/\1/g' | awk '!seen[$0]++' |
    while IFS= read -r LINK
  do
    # Enter The URL & Subsequent LINKS Into The Database While We Crawl.
    mysql -u$DB_USER -p$DB_PASS -D$DB_NAME -e "INSERT INTO URLS (URL, LINKS, TIMESTAMP) VALUES('$TargetURL', '$LINK','$TIMESTAMP');"
done

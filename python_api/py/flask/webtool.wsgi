activate_this = '/var/www/flask/venv/bin/activate_this.py'
with open(activate_this) as file_:
    exec(file_.read(), dict(__file__=activate_this))

import sys
 
sys.path.append('/var/www/flask')
 
from webtool import app as application

from flask import Flask, render_template ##import flask class and function
from models import HistoricalData
from controller1 import Controller1

app = Flask(__name__) #create new instance and name it app


@app.route("/") #map the url to the function index which returns index.html
def index():
  return render_template("index.html")

@app.route("/about")
def about():
  animal = "dog"
  number = 5
  return render_template("about.html",animal=animal,number=number)

@app.route("/test")
def corr():  
  data = HistoricalData()
  data.connectDB()
  data = data.getData()
  
  return render_template("test.html",number=data)


@app.after_request
def add_header(r):
    """
    Add headers to both force latest IE rendering engine or Chrome Frame,
    and also to cache the rendered page for 10 minutes.
    """
    r.headers["Cache-Control"] = "no-cache, no-store, must-revalidate, public, max-age=0"
    r.headers["Pragma"] = "no-cache"
    r.headers["Expires"] = "0"
    return r

if __name__ == "__main__": 
  app.run(debug=True)
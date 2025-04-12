var express = require('express');
var path = require('path');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var methodOverride = require('method-override');

var db = require('./models/db');
var Project = require('./models/Project');

var indexRouter = require('./routes/index');
var projectsRouter = require('./routes/projects');

var app = express();

// Povezivanje s MongoDB bazom podataka
const mongoose = require('mongoose');
mongoose.connect('mongodb://localhost/projectsDB', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

// Middleware za method override (PUT i DELETE preko POST)
app.use(methodOverride('_method'));

// View engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

// Middleware za logiranje, parsiranje tijela zahtjeva i kolačića
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

// Rute za aplikaciju
app.use('/', indexRouter);
app.use('/projects', projectsRouter);

// Error handleri za 404 i ostale greške
app.use(function (req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

if (app.get('env') === 'development') {
  app.use(function (err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err,
    });
  });
}

app.use(function (err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {},
  });
});

module.exports = app;

var express = require('express');
var path = require('path');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var methodOverride = require('method-override');
var session = require('express-session');

var db = require('./models/db');
const User = require('./models/User'); 

var Project = require('./models/Project');

var indexRouter = require('./routes/index');
var projectsRouter = require('./routes/projects');
var registerRouter = require('./routes/register');
var loginRouter = require('./routes/login');
var logoutRouter = require('./routes/logout'); 

var app = express();

const mongoose = require('mongoose');
mongoose.connect('mongodb://localhost/projectsDB', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
})
.then(() => console.log('Connected to MongoDB'))
.catch(err => console.error('MongoDB connection error:', err));

app.use(methodOverride('_method'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use(session({
  secret: 'secret_key', 
  resave: false, 
  saveUninitialized: false, 
  cookie: { maxAge: 1000 * 60 * 60 * 24 } 
}));

app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

app.use(logger('dev'));
app.use('/', indexRouter);
app.use('/projects', projectsRouter);
app.use('/register', registerRouter); 
app.use('/login', loginRouter); 
app.use('/logout', logoutRouter);

app.use((req, res, next) => {
  res.status(404).render('404', {
    title: 'Stranica nije pronađena',
    url: req.url
  });
});

app.use((err, req, res, next) => {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: app.get('env') === 'development' ? err : {}
  });
});

module.exports = app;

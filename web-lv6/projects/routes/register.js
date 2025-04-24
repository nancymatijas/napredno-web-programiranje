const express = require('express');
const router = express.Router();
const User = require('../models/User');
const bcrypt = require('bcrypt');

router.get('/', (req, res) => {
  res.render('register', { 
    title: 'Registracija',
    error: null,
    data: { username: '', email: '', password: '' }
  });
});

router.post('/', async (req, res) => {
  const { username, email, password } = req.body;

  try {
    const existingUser = await User.findOne({ 
      $or: [
        { username: username.toLowerCase() },
        { email: email.toLowerCase() }
      ]
    });

    if (existingUser) {
      return res.render('register', {
        title: 'Registracija',
        error: 'Korisničko ime ili email već postoje!',
        data: { username, email, password }
      });
    }

    const newUser = new User({ 
      username: username.trim().toLowerCase(),
      email: email.trim().toLowerCase(),
      password
    });

    await newUser.save();
    res.redirect('/login');

  } catch (err) {
    console.error('Greška pri registraciji:', err);
    res.render('register', {
      title: 'Registracija',
      error: 'Došlo je do greške prilikom registracije',
      data: { username, email, password }
    });
  }
});

module.exports = router;

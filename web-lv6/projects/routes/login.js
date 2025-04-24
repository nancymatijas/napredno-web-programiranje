const express = require('express');
const router = express.Router();
const User = require('../models/User');

router.get('/', (req, res) => {
  res.render('login', { 
    title: 'Prijava',
    error: null,
    data: { usernameOrEmail: '', password: '' }
  });
});

router.post('/', async (req, res) => {
  const { usernameOrEmail, password } = req.body;
  const cleanedInput = usernameOrEmail.trim().toLowerCase();

  try {
    const user = await User.findOne({
      $or: [
        { username: cleanedInput },
        { email: cleanedInput }
      ]
    });

    if (!user) {
      return res.render('login', {
        title: 'Prijava',
        error: 'Neispravni podaci za prijavu!',
        data: { usernameOrEmail, password }
      });
    }

    const isPasswordValid = await user.comparePassword(password);
    if (!isPasswordValid) {
      return res.render('login', {
        title: 'Prijava',
        error: 'Neispravni podaci za prijavu!',
        data: { usernameOrEmail, password }
      });
    }

    req.session.user = {
      id: user._id,
      username: user.username
    };

    res.redirect('/projects');

  } catch (err) {
    console.error('Greška pri prijavi:', err);
    res.render('login', {
      title: 'Prijava',
      error: 'Došlo je do greške prilikom prijave',
      data: { usernameOrEmail, password }
    });
  }
});

module.exports = router;

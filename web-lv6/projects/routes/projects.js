const express = require('express');
const router = express.Router();
const mongoose = require('mongoose');
const Project = mongoose.model('Project');
const User = mongoose.model('User');

const requireAuth = (req, res, next) => {
  if (!req.session.user) return res.redirect('/login');
  next();
};

router.get('/', async (req, res) => {
  try {
    const projects = await Project.find({})
      .populate('author', 'username')
      .populate('teamMembers', 'username');
    
    res.render('projects/index', { 
      projects,
      currentUser: req.session.user 
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: 'Došlo je do greške' });
  }
});

// Ruta za projekte gdje je korisnik voditelj
router.get('/leader', requireAuth, async (req, res) => {
  try {
    const projects = await Project.find({ author: req.session.user.id })
      .populate('teamMembers', 'username');
    
    res.render('projects/leader', {
      projects,
      currentUser: req.session.user
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: 'Došlo je do greške' });
  }
});

// Ruta za projekte gdje je korisnik član tima
router.get('/member', requireAuth, async (req, res) => {
  try {
    const projects = await Project.find({ teamMembers: req.session.user.id })
      .populate('author', 'username');
    
    res.render('projects/member', {
      projects,
      currentUser: req.session.user
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: 'Došlo je do greške' });
  }
});

// Forma za novi projekt
router.get('/new', requireAuth, async (req, res) => {
  try {
    const users = await User.find({ _id: { $ne: req.session.user.id } });
    res.render('projects/new', {
      users,
      currentUser: req.session.user
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: err.message });
  }
});

// Kreiranje novog projekta
router.post('/', requireAuth, async (req, res) => {
  try {
    const teamMembers = Array.isArray(req.body.teamMembers)
      ? req.body.teamMembers
      : req.body.teamMembers ? [req.body.teamMembers] : [];

    const project = new Project({
      name: req.body.name,
      description: req.body.description,
      price: req.body.price,
      startDate: new Date(req.body.startDate),
      endDate: new Date(req.body.endDate),
      tasksCompleted: req.body.tasksCompleted || '',
      teamMembers,
      author: req.session.user.id,
      archived: false
    });

    await project.save();
    res.redirect('/projects/leader');
  } catch (err) {
    console.error(err);
    const users = await User.find({ _id: { $ne: req.session.user.id } });
    res.render('projects/new', {
      users,
      error: err.message,
      project: req.body,
      currentUser: req.session.user
    });
  }
});

// Ruta kojom se prikazuju arhivirani projekti
router.get('/archive', requireAuth, async (req, res) => {
  try {
    const userId = req.session.user.id;
    const projects = await Project.find({
      archived: true,
      $or: [
        { author: userId },
        { teamMembers: userId }
      ]
    })
    .populate('author', 'username')
    .populate('teamMembers', 'username');

    res.render('projects/archive', {
      projects,
      currentUser: req.session.user
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: 'Došlo je do greške' });
  }
});

// Prikaz pojedinačnog projekta
router.get('/:id', async (req, res) => {
  try {
    const project = await Project.findById(req.params.id)
      .populate('author', 'username')
      .populate('teamMembers', 'username');

    if (!project) return res.status(404).send('Projekt nije pronađen');
    
    res.render('projects/show', { 
      project,
      currentUser: req.session.user 
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: 'Došlo je do greške' });
  }
});

// Forma za uređivanje projekta (samo za voditelje)
router.get('/:id/edit', requireAuth, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    
    if (project.author.toString() !== req.session.user.id) {
      return res.status(403).render('error', { 
        error: 'Nemate ovlasti za uređivanje ovog projekta' 
      });
    }

    const users = await User.find({ _id: { $ne: req.session.user.id } });
    
    users.forEach(user => {
      user.isSelected = project.teamMembers.includes(user._id.toString());
    });

    res.render('projects/edit', { 
      project,
      users,
      currentUser: req.session.user
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: err.message });
  }
});

// Ažuriranje projekta (samo za voditelje)
router.put('/:id', requireAuth, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    
    if (project.author.toString() !== req.session.user.id) {
      return res.status(403).render('error', { 
        error: 'Nemate ovlasti za uređivanje ovog projekta' 
      });
    }

    const teamMembers = Array.isArray(req.body.teamMembers) 
      ? req.body.teamMembers 
      : req.body.teamMembers ? [req.body.teamMembers] : [];

    const updatedProject = await Project.findByIdAndUpdate(
      req.params.id,
      {
        name: req.body.name,
        description: req.body.description,
        price: req.body.price,
        startDate: new Date(req.body.startDate),
        endDate: new Date(req.body.endDate),
        tasksCompleted: req.body.tasksCompleted || '',
        teamMembers,
        archived: req.body.archived === 'on'
      },
      { runValidators: true, new: true }
    );

    res.redirect(`/projects/${updatedProject._id}`);
  } catch (err) {
    console.error(err);
    const users = await User.find({ _id: { $ne: req.session.user.id } });
    const project = await Project.findById(req.params.id);
    
    res.render('projects/edit', {
      users,
      project,
      error: err.message,
      currentUser: req.session.user
    });
  }
});

// Forma za uređivanje obavljenih poslova (za članove tima)
router.get('/:id/edit-member', requireAuth, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);

    if (!project.teamMembers.includes(req.session.user.id)) {
      return res.status(403).render('error', { error: 'Niste član ovog projektnog tima.' });
    }

    if (project.archived) {
      return res.status(403).render('error', { error: 'Projekt je arhiviran. Uređivanje nije dopušteno.' });
    }

    res.render('projects/edit_member', { 
      project,
      currentUser: req.session.user
    });
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: err.message });
  }
});

// Ažuriranje obavljenih poslova (za članove tima)
router.put('/:id/edit-member', requireAuth, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);

    if (!project.teamMembers.includes(req.session.user.id)) {
      return res.status(403).render('error', { error: 'Niste član ovog projektnog tima.' });
    }

    if (project.archived) {
      return res.status(403).render('error', { error: 'Projekt je arhiviran. Uređivanje nije dopušteno.' });
    }

    await Project.findByIdAndUpdate(
      req.params.id,
      { tasksCompleted: req.body.tasksCompleted || '' },
      { runValidators: true }
    );

    res.redirect('/projects/member');
  } catch (err) {
    console.error(err);
    res.status(500).render('error', { error: err.message });
  }
});

// Brisanje projekta
router.delete('/:id', requireAuth, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    
    if (project.author.toString() !== req.session.user.id) {
      return res.status(403).render('error', { 
        error: 'Nemate ovlasti za brisanje ovog projekta' 
      });
    }

    await Project.findByIdAndDelete(req.params.id);
    res.redirect('/projects');
  } catch (err) {
    console.error(err);
    res.status(500).send(err.message);
  }
});

// Odjava korisnika
router.get('/logout', (req, res) => {
  req.session.destroy(err => {
    if (err) {
      console.error('Greška pri odjavi:', err);
      return res.status(500).render('error', { 
        error: 'Došlo je do greške pri odjavi' 
      });
    }
    res.redirect('/login');
  });
});

module.exports = router;

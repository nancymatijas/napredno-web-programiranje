const express = require('express');
const router = express.Router();
const mongoose = require('mongoose');
const Project = mongoose.model('Project');

// Pregled svih projekata (GET /projects)
router.get('/', async (req, res) => {
  try {
    const projects = await Project.find({});
    res.render('projects/index', { projects });
  } catch (err) {
    res.status(500).send(err.message);
  }
});

// Forma za dodavanje novog projekta (GET /projects/new)
router.get('/new', (req, res) => {
  res.render('projects/new');
});

// Dodavanje novog projekta (POST /projects)
router.post('/', async (req, res) => {
  const teamMembers = req.body.teamMembers 
    ? req.body.teamMembers.split(',').map(m => m.trim())
    : [];

  try {
    await Project.create({
      name: req.body.name,
      description: req.body.description,
      price: req.body.price,
      tasksCompleted: req.body.tasksCompleted,
      startDate: req.body.startDate,
      endDate: req.body.endDate,
      teamMembers,
    });
    res.redirect('/projects');
  } catch (err) {
    res.render('projects/new', { error: err.message });
  }
});


// Pregled pojedinačnog projekta (GET /projects/:id)
router.get('/:id', async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    if (!project) return res.status(404).send('Projekt nije pronađen');
    res.render('projects/show', { project });
  } catch (err) {
    res.status(500).send(err.message);
  }
});

// Forma za uređivanje projekta (GET /projects/:id/edit)
router.get('/:id/edit', async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    if (!project) return res.redirect('/projects');
    res.render('projects/edit', { project });
  } catch (err) {
    res.status(500).send(err.message);
  }
});

// Ažuriranje projekta (PUT /projects/:id)
router.put('/:id', async (req, res) => {
  const teamMembers = req.body.teamMembers 
    ? req.body.teamMembers.split(',').map(m => m.trim())
    : [];

  try {
    await Project.findByIdAndUpdate(req.params.id, {
      name: req.body.name,
      description: req.body.description,
      price: req.body.price,
      tasksCompleted: req.body.tasksCompleted,
      startDate: req.body.startDate,
      endDate: req.body.endDate,
      teamMembers,
    });
    res.redirect(`/projects/${req.params.id}`);
  } catch (err) {
    res.status(500).send(err.message);
  }
});

// Brisanje projekta (DELETE /projects/:id)
router.delete('/:id', async (req, res) => {
  try {
    await Project.findByIdAndDelete(req.params.id);
    res.redirect('/projects');
  } catch (err) {
    res.status(500).send(err.message);
  }
});

module.exports = router;

const mongoose = require('mongoose');

const projectSchema = new mongoose.Schema({
  name: { type: String, required: true },
  description: String,
  price: Number,
  tasksCompleted: String,
  startDate: { type: Date, default: Date.now },
  endDate: Date,
  teamMembers: [String]
});

module.exports = mongoose.model('Project', projectSchema);

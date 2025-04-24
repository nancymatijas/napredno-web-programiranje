const mongoose = require('mongoose');

const ProjectSchema = new mongoose.Schema({
  name: { type: String, required: true },
  description: String,
  price: { type: Number, required: true },
  tasksCompleted: { type: String, default: '' }, 
  startDate: { type: Date, required: true },
  endDate: { type: Date, required: true },
  teamMembers: [{ 
    type: mongoose.Schema.Types.ObjectId, 
    ref: 'User' 
  }],
  author: { 
    type: mongoose.Schema.Types.ObjectId, 
    ref: 'User', 
    required: true 
  },
  archived: { type: Boolean, default: false }
}, { timestamps: true });

module.exports = mongoose.model('Project', ProjectSchema);

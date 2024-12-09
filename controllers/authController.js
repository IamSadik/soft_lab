const { getConsumerByEmail, getStakeholderByEmail } = require('../models/authModel');
//const bcrypt = require('bcrypt');

exports.loginConsumer = async (req, res) => {
    const { email, password } = req.body;
    try {
        const consumer = await getConsumerByEmail(email);
        if (!consumer) {
            return res.status(404).json({ message: 'Consumer not found' });
        }

        // Directly compare passwords since they are not hashed
        if (password !== consumer.password) {
            return res.status(401).json({ message: 'Invalid email or password' });
        }

        // If login successful, redirect to consumer dashboard
        res.status(200).json({ redirectURL: '/consumer/dashboard' });

    } catch (error) {
        console.error('Error in consumer login:', error);
        res.status(500).json({ message: 'Internal Server Error' });
    }
};


exports.loginStakeholder = async (req, res) => {
    const { email, password } = req.body;
    try {
        const stakeholder = await getStakeholderByEmail(email);
        if (!stakeholder) {
            return res.status(404).json({ message: 'Stakeholder not found' });
        }

        // Directly compare passwords since they are not hashed
        if (password !== stakeholder.password) {
            return res.status(401).json({ message: 'Invalid email or password' });
        }

        // If login successful, redirect to stakeholder dashboard
        res.status(200).json({ redirectURL: '/stakeholder/dashboard' });
    } catch (error) {
        console.error('Error in stakeholder login:', error);
        res.status(500).json({ message: 'Internal Server Error' });
    }
};


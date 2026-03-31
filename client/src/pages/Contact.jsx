import React, { useState } from 'react';
import Navbar from '../components/Navbar';
import axios from 'axios';

const Contact = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
  });
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState('');

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setSuccess(false);

    try {
      const response = await axios.post('/api/contact', formData); // eslint-disable-line no-unused-vars
      setSuccess(true);
      setFormData({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      });
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to send message. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="contact-page">
      <Navbar />
      
      <div className="contact-container">
        <div className="contact-header">
          <h1>Contact Us</h1>
          <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        {success && (
          <div className="success-message">
            <h3>Thank you for your message!</h3>
            <p>We've received your message and will get back to you soon.</p>
          </div>
        )}

        {error && (
          <div className="error-message">
            <p>{error}</p>
          </div>
        )}

        <form className="contact-form" onSubmit={handleSubmit}>
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="name">Name *</label>
              <input
                type="text"
                id="name"
                name="name"
                value={formData.name}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="email">Email *</label>
              <input
                type="email"
                id="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
              />
            </div>
          </div>

          <div className="form-group">
            <label htmlFor="phone">Phone Number</label>
            <input
              type="tel"
              id="phone"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
            />
          </div>

          <div className="form-group">
            <label htmlFor="subject">Subject *</label>
            <input
              type="text"
              id="subject"
              name="subject"
              value={formData.subject}
              onChange={handleChange}
              required
            />
          </div>

          <div className="form-group">
            <label htmlFor="message">Message *</label>
            <textarea
              id="message"
              name="message"
              value={formData.message}
              onChange={handleChange}
              rows="6"
              required
            ></textarea>
          </div>

          <button type="submit" className="submit-btn" disabled={loading}>
            {loading ? 'Sending...' : 'Send Message'}
          </button>
        </form>

        <div className="contact-info">
          <div className="info-item">
            <h3>📍 Address</h3>
            <p>123 Medical Center Drive<br />
            Dhaka, Bangladesh 1000</p>
          </div>

          <div className="info-item">
            <h3>📞 Phone</h3>
            <p>+880 1234 567890<br />
            +880 9876 543210</p>
          </div>

          <div className="info-item">
            <h3>✉️ Email</h3>
            <p>info@bloodlink.com<br />
            support@bloodlink.com</p>
          </div>

          <div className="info-item">
            <h3>🕐 Hours</h3>
            <p>Monday - Friday: 9:00 AM - 6:00 PM<br />
            Saturday: 10:00 AM - 4:00 PM<br />
            Sunday: Closed</p>
          </div>
        </div>
      </div>

      <style jsx>{`
        .contact-page {
          min-height: 100vh;
          background: var(--grey);
        }

        .contact-container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 120px 2rem 2rem;
        }

        .contact-header {
          text-align: center;
          margin-bottom: 3rem;
        }

        .contact-header h1 {
          font-size: 2.5rem;
          color: var(--red);
          margin-bottom: 1rem;
        }

        .contact-header p {
          font-size: 1.1rem;
          color: var(--text);
          max-width: 600px;
          margin: 0 auto;
        }

        .success-message {
          background: #d4edda;
          color: #155724;
          padding: 1rem;
          border-radius: 8px;
          margin-bottom: 2rem;
          text-align: center;
          border: 1px solid #c3e6cb;
        }

        .error-message {
          background: #f8d7da;
          color: #721c24;
          padding: 1rem;
          border-radius: 8px;
          margin-bottom: 2rem;
          text-align: center;
          border: 1px solid #f5c6cb;
        }

        .contact-form {
          background: white;
          padding: 2rem;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          margin-bottom: 3rem;
        }

        .form-row {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 1rem;
        }

        .form-group {
          margin-bottom: 1.5rem;
        }

        .form-group label {
          display: block;
          margin-bottom: 0.5rem;
          font-weight: 600;
          color: var(--text);
        }

        .form-group input,
        .form-group textarea {
          width: 100%;
          padding: 0.75rem;
          border: 2px solid #e1e5e9;
          border-radius: 8px;
          font-size: 1rem;
          transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
          outline: none;
          border-color: var(--red);
        }

        .submit-btn {
          background: var(--red);
          color: white;
          padding: 1rem 2rem;
          border: none;
          border-radius: 8px;
          font-size: 1rem;
          font-weight: 600;
          cursor: pointer;
          transition: background-color 0.3s;
        }

        .submit-btn:hover:not(:disabled) {
          background: var(--red-dark);
        }

        .submit-btn:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }

        .contact-info {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 2rem;
        }

        .info-item {
          background: white;
          padding: 2rem;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          text-align: center;
        }

        .info-item h3 {
          color: var(--red);
          margin-bottom: 1rem;
          font-size: 1.2rem;
        }

        .info-item p {
          color: var(--text);
          line-height: 1.6;
        }

        @media (max-width: 768px) {
          .contact-container {
            padding: 100px 1rem 1rem;
          }

          .form-row {
            grid-template-columns: 1fr;
          }

          .contact-info {
            grid-template-columns: 1fr;
          }

          .contact-header h1 {
            font-size: 2rem;
          }
        }
      `}</style>
    </div>
  );
};

export default Contact;

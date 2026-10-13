import React, { useState } from 'react';
import { X, CheckCircle, Calendar, Send } from 'lucide-react';

interface InquiryModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export const InquiryModal: React.FC<InquiryModalProps> = ({ isOpen, onClose }) => {
  const [submitted, setSubmitted] = useState(false);
  const [formData, setFormData] = useState({
    studentName: '',
    grade: '',
    parentName: '',
    phone: '',
    email: '',
    campus: 'Islamabad Campus (H-8/4)',
    preferredDate: '',
    notes: ''
  });

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitted(true);
  };

  const handleResetAndClose = () => {
    setSubmitted(false);
    onClose();
  };

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-dark/80 backdrop-blur-md animate-in fade-in"
      role="dialog"
      aria-modal="true"
    >
      <div className="bg-white rounded-3xl shadow-2xl border border-navy/10 max-w-xl w-full overflow-hidden relative animate-in zoom-in-95">
        {/* Modal Header */}
        <div className="bg-navy text-white px-8 py-6 flex items-center justify-between">
          <div>
            <div className="text-[11px] font-bold text-cyan-light uppercase tracking-wider mb-1">
              Academic Session 2026–2027
            </div>
            <h3 className="font-display text-xl font-bold">
              Book a Private Campus Walkthrough & Inquiry
            </h3>
          </div>
          <button
            onClick={handleResetAndClose}
            className="w-8 h-8 rounded-full bg-white/10 hover:bg-rose-500 flex items-center justify-center text-white transition-colors"
            aria-label="Close modal"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Modal Content */}
        <div className="p-8">
          {submitted ? (
            <div className="text-center py-8">
              <div className="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                <CheckCircle className="w-8 h-8" />
              </div>
              <h4 className="font-display text-2xl font-bold text-navy mb-2">
                Inquiry & Tour Slot Reserved!
              </h4>
              <p className="text-slate-600 text-sm leading-relaxed max-w-md mx-auto mb-8">
                Thank you, <strong>{formData.parentName || 'Parent'}</strong>. Our Admissions Directorate will contact you via WhatsApp and telephone (+92 {formData.phone || 'your phone'}) within 24 hours to confirm your private campus appointment for <strong>{formData.studentName || 'the scholar'}</strong>.
              </p>
              <button
                onClick={handleResetAndClose}
                className="px-8 py-3 bg-navy text-white rounded-full font-display font-bold text-xs uppercase tracking-wider hover:bg-navy-dark transition-all"
              >
                Return to School Homepage
              </button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-navy mb-1.5">
                    Student's Full Name *
                  </label>
                  <input
                    type="text"
                    required
                    value={formData.studentName}
                    onChange={(e) => setFormData({ ...formData, studentName: e.target.value })}
                    placeholder="e.g. Daniyal Khan"
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-navy mb-1.5">
                    Applying For Stage / Grade *
                  </label>
                  <select
                    required
                    value={formData.grade}
                    onChange={(e) => setFormData({ ...formData, grade: e.target.value })}
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20 bg-white"
                  >
                    <option value="">Select Grade Level</option>
                    <option value="Early Years (Ages 2.5–5)">Early Years (Ages 2.5–5)</option>
                    <option value="Primary School (Grades 1–5)">Primary School (Grades 1–5)</option>
                    <option value="Middle School (Grades 6–8)">Middle School (Grades 6–8)</option>
                    <option value="Cambridge IGCSE & O Levels">Cambridge IGCSE & O Levels</option>
                    <option value="Cambridge A Levels">Cambridge A Levels</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-navy mb-1.5">
                    Parent / Guardian Name *
                  </label>
                  <input
                    type="text"
                    required
                    value={formData.parentName}
                    onChange={(e) => setFormData({ ...formData, parentName: e.target.value })}
                    placeholder="e.g. Dr. Tariq Mahmood"
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-navy mb-1.5">
                    WhatsApp / Phone Number *
                  </label>
                  <input
                    type="tel"
                    required
                    value={formData.phone}
                    onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                    placeholder="+92 300 1234567"
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-navy mb-1.5">
                    Parent Email Address *
                  </label>
                  <input
                    type="email"
                    required
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder="parent@example.com"
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-navy mb-1.5">
                    Preferred Campus Location *
                  </label>
                  <select
                    value={formData.campus}
                    onChange={(e) => setFormData({ ...formData, campus: e.target.value })}
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20 bg-white"
                  >
                    <option value="Islamabad Campus (H-8/4)">Islamabad Campus (Sector H-8/4)</option>
                    <option value="Lahore Campus (DHA Phase 5)">Lahore Campus (DHA Phase 5)</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-navy mb-1.5">
                  Preferred Tour Date / Additional Notes
                </label>
                <input
                  type="text"
                  value={formData.notes}
                  onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                  placeholder="e.g. Saturday morning preferred; interested in STEM scholarship"
                  className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan focus:ring-2 focus:ring-cyan/20"
                />
              </div>

              <div className="pt-2">
                <button
                  type="submit"
                  className="w-full py-3.5 bg-navy text-white rounded-xl font-display font-bold text-sm tracking-wide hover:bg-navy-dark shadow-lg transition-all"
                >
                  Submit Flight Plan & Confirm Booking
                </button>
              </div>
            </form>
          )}
        </div>
      </div>
    </div>
  );
};

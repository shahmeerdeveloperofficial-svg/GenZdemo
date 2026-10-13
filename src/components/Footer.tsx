import React, { useState } from 'react';
import {
  MapPin,
  Phone,
  Mail,
  ArrowUp,
  Instagram,
  Facebook,
  Linkedin,
  Youtube,
  Send,
  CheckCircle2,
  Globe2,
  Award,
  BookOpen,
} from 'lucide-react';

interface FooterProps {
  onOpenModal: () => void;
}

export const Footer: React.FC<FooterProps> = ({ onOpenModal }) => {
  const [email, setEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  const handleSubscribe = (e: React.FormEvent) => {
    e.preventDefault();
    if (email.trim()) {
      setSubscribed(true);
      setEmail('');
      setTimeout(() => setSubscribed(false), 4500);
    }
  };

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <footer className="bg-[#07172C] text-white pt-20 pb-12 relative overflow-hidden border-t border-cyan/20" role="contentinfo">
      {/* Cinematic Campus Architecture Background Image */}
      <div className="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <img
          src="/assets/images/hero-campus.jpg"
          alt="GenZ Campus Architecture"
          className="w-full h-full object-cover opacity-20 mix-blend-luminosity filter blur-[0.5px] scale-105"
        />
        {/* Deep Multi-stop Midnight Overlay for crystal clear text readability */}
        <div className="absolute inset-0 bg-gradient-to-b from-[#07172C]/95 via-[#07172C]/88 to-[#040D1B]/98" />
        <div className="absolute top-0 left-1/4 w-96 h-96 bg-cyan/15 rounded-full blur-[140px]" />
        <div className="absolute bottom-10 right-1/4 w-96 h-96 bg-blue-600/15 rounded-full blur-[140px]" />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Top Newsletter & Dialogue Strip */}
        <div className="bg-gradient-to-r from-[#0B2545] to-[#0d2e54] p-8 sm:p-12 rounded-3xl border border-white/10 shadow-2xl mb-16 relative overflow-hidden">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div className="lg:col-span-7">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan/15 text-cyan-light text-xs font-bold uppercase tracking-wider mb-3">
                <Send className="w-3.5 h-3.5" />
                <span>The GenZ International Dispatch</span>
              </div>
              <h3 className="font-display text-2xl sm:text-3xl font-extrabold text-white mb-2">
                Stay Ahead in Global Education & Admissions
              </h3>
              <p className="text-white/70 text-sm max-w-xl">
                Receive key term updates, Cambridge symposium announcements, scholarship notifications, and scholar accomplishments directly.
              </p>
            </div>

            <div className="lg:col-span-5">
              <form onSubmit={handleSubscribe} className="flex flex-col sm:flex-row gap-3">
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="Enter parent or guardian email..."
                  required
                  className="flex-1 px-5 py-3.5 rounded-full bg-white/10 border border-white/20 text-white placeholder-white/50 text-sm focus:outline-none focus:border-cyan transition-colors"
                />
                <button
                  type="submit"
                  className="px-7 py-3.5 bg-cyan hover:bg-cyan-light text-navy font-display font-bold text-xs uppercase tracking-wider rounded-full shadow-lg hover:shadow-cyan-glow transition-all"
                >
                  Subscribe
                </button>
              </form>
              {subscribed && (
                <div className="flex items-center gap-2 text-cyan-light text-xs mt-3 font-semibold">
                  <CheckCircle2 className="w-4 h-4 text-cyan" />
                  <span>Thank you for subscribing! You are now on the GenZ priority dispatch.</span>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Main 4-Column Directory Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-16 border-b border-white/10">
          {/* Column 1: School Identity & Accreditations (Col Span 4) */}
          <div className="lg:col-span-4 flex flex-col items-start">
            <div className="flex items-center gap-4 mb-5">
              <img
                src="/assets/images/genz-logo.png"
                alt="GenZ International School Logo"
                className="h-16 sm:h-20 w-auto bg-white p-2 rounded-2xl shadow-lg object-contain"
              />
              <div>
                <span className="font-display font-extrabold text-xl text-white tracking-tight block">
                  GenZ International
                </span>
                <span className="text-[10px] text-cyan uppercase tracking-widest font-bold">
                  Cambridge World School
                </span>
              </div>
            </div>

            <p className="text-white/70 text-sm leading-relaxed mb-6 max-w-sm">
              Authorised British Cambridge education cultivating visionary leaders, critical thinkers, and ethical global citizens from Early Years through A-Levels.
            </p>

            {/* Accreditations Row */}
            <div className="space-y-2.5 w-full">
              <div className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10">
                <Award className="w-4 h-4 text-cyan flex-shrink-0" />
                <span className="text-xs text-white/80 font-semibold">
                  Cambridge Assessment International Education (PK882)
                </span>
              </div>
              <div className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10">
                <Globe2 className="w-4 h-4 text-cyan flex-shrink-0" />
                <span className="text-xs text-white/80 font-semibold">
                  College Board PSAT/SAT Official Testing Center
                </span>
              </div>
            </div>
          </div>

          {/* Column 2: Academic Continuum (Col Span 3) */}
          <div className="lg:col-span-3">
            <h4 className="font-display font-bold text-base text-white uppercase tracking-wider mb-5 flex items-center gap-2">
              <BookOpen className="w-4 h-4 text-cyan" />
              <span>Academic Continuum</span>
            </h4>
            <ul className="space-y-3 text-sm text-white/70">
              <li>
                <a href="#continuum" className="hover:text-cyan transition-colors flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-cyan/60" />
                  <span>Early Years (Ages 2.5–5)</span>
                </a>
              </li>
              <li>
                <a href="#continuum" className="hover:text-cyan transition-colors flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-cyan/60" />
                  <span>Cambridge Primary (Ages 5–11)</span>
                </a>
              </li>
              <li>
                <a href="#continuum" className="hover:text-cyan transition-colors flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-cyan/60" />
                  <span>Cambridge Lower Secondary (Ages 11–14)</span>
                </a>
              </li>
              <li>
                <a href="#continuum" className="hover:text-cyan transition-colors flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-cyan/60" />
                  <span>Cambridge IGCSE & O-Levels</span>
                </a>
              </li>
              <li>
                <a href="#continuum" className="hover:text-cyan transition-colors flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-cyan/60" />
                  <span>Cambridge International A-Levels</span>
                </a>
              </li>
              <li>
                <a href="#continuum" className="hover:text-cyan transition-colors flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-cyan/60" />
                  <span>University Flight Counseling</span>
                </a>
              </li>
            </ul>
          </div>

          {/* Column 3: Admissions & Life (Col Span 2) */}
          <div className="lg:col-span-2">
            <h4 className="font-display font-bold text-base text-white uppercase tracking-wider mb-5">
              Admissions & Life
            </h4>
            <ul className="space-y-3 text-sm text-white/70">
              <li>
                <a href="#admissions" className="hover:text-cyan transition-colors block">
                  Admissions Criteria
                </a>
              </li>
              <li>
                <button onClick={onOpenModal} className="hover:text-cyan transition-colors text-left block">
                  Book Campus Visit
                </button>
              </li>
              <li>
                <a href="#campus" className="hover:text-cyan transition-colors block">
                  Athletics & Sports
                </a>
              </li>
              <li>
                <a href="#campus" className="hover:text-cyan transition-colors block">
                  Student Societies & MUN
                </a>
              </li>
              <li>
                <a href="#campus" className="hover:text-cyan transition-colors block">
                  STEM & Robotics Hub
                </a>
              </li>
              <li>
                <button onClick={onOpenModal} className="hover:text-cyan transition-colors text-left block">
                  School Fee Structure
                </button>
              </li>
            </ul>
          </div>

          {/* Column 4: Campus Coordinates & Contact (Col Span 3) */}
          <div className="lg:col-span-3">
            <h4 className="font-display font-bold text-base text-white uppercase tracking-wider mb-5 flex items-center gap-2">
              <MapPin className="w-4 h-4 text-cyan" />
              <span>Campus Locations</span>
            </h4>
            <div className="space-y-4 text-sm text-white/80">
              {/* Islamabad Campus */}
              <div className="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                <div className="font-bold text-white text-xs uppercase tracking-wider text-cyan-light mb-1">
                  Islamabad Campus
                </div>
                <p className="text-xs text-white/70 leading-snug mb-2">
                  Sector H-8 / Diplomatic Enclave, Islamabad, Pakistan
                </p>
                <div className="flex items-center gap-2 text-xs font-semibold text-white/90">
                  <Phone className="w-3.5 h-3.5 text-cyan" />
                  <span>+92 51 111-436-901</span>
                </div>
              </div>

              {/* Lahore Campus */}
              <div className="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                <div className="font-bold text-white text-xs uppercase tracking-wider text-cyan-light mb-1">
                  Lahore Campus
                </div>
                <p className="text-xs text-white/70 leading-snug mb-2">
                  Sector Y, Phase 3, DHA, Lahore, Pakistan
                </p>
                <div className="flex items-center gap-2 text-xs font-semibold text-white/90">
                  <Phone className="w-3.5 h-3.5 text-cyan" />
                  <span>+92 42 111-436-902</span>
                </div>
              </div>

              {/* Admissions Email */}
              <div className="flex items-center gap-2 text-xs text-white/80 pt-1">
                <Mail className="w-3.5 h-3.5 text-cyan" />
                <a href="mailto:admissions@genz.edu.pk" className="hover:text-cyan transition-colors">
                  admissions@genz.edu.pk
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Social & Legal Bar */}
        <div className="pt-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-white/60">
          {/* Social Media Pill Badges */}
          <div className="flex items-center gap-3">
            <a
              href="https://instagram.com"
              target="_blank"
              rel="noreferrer"
              className="w-10 h-10 rounded-full bg-white/10 hover:bg-cyan hover:text-navy flex items-center justify-center transition-all duration-300 shadow-sm"
              aria-label="Instagram"
            >
              <Instagram className="w-4 h-4" />
            </a>
            <a
              href="https://facebook.com"
              target="_blank"
              rel="noreferrer"
              className="w-10 h-10 rounded-full bg-white/10 hover:bg-cyan hover:text-navy flex items-center justify-center transition-all duration-300 shadow-sm"
              aria-label="Facebook"
            >
              <Facebook className="w-4 h-4" />
            </a>
            <a
              href="https://linkedin.com"
              target="_blank"
              rel="noreferrer"
              className="w-10 h-10 rounded-full bg-white/10 hover:bg-cyan hover:text-navy flex items-center justify-center transition-all duration-300 shadow-sm"
              aria-label="LinkedIn"
            >
              <Linkedin className="w-4 h-4" />
            </a>
            <a
              href="https://youtube.com"
              target="_blank"
              rel="noreferrer"
              className="w-10 h-10 rounded-full bg-white/10 hover:bg-cyan hover:text-navy flex items-center justify-center transition-all duration-300 shadow-sm"
              aria-label="YouTube"
            >
              <Youtube className="w-4 h-4" />
            </a>
          </div>

          {/* Copyright & Legal Links */}
          <div className="flex flex-wrap items-center justify-center gap-4 text-center">
            <span>© 2026 GenZ International School. All Rights Reserved.</span>
            <span className="hidden sm:inline">·</span>
            <button onClick={onOpenModal} className="hover:text-cyan transition-colors">
              Privacy Policy
            </button>
            <span>·</span>
            <button onClick={onOpenModal} className="hover:text-cyan transition-colors">
              Safeguarding Policy
            </button>
            <span>·</span>
            <button onClick={onOpenModal} className="hover:text-cyan transition-colors">
              Terms of Enrolment
            </button>
          </div>

          {/* Back to top button */}
          <button
            onClick={scrollToTop}
            className="inline-flex items-center gap-2 hover:text-cyan transition-colors group"
          >
            <span className="font-semibold text-xs">Back to Top</span>
            <div className="w-8 h-8 rounded-full bg-cyan/20 group-hover:bg-cyan text-cyan group-hover:text-navy flex items-center justify-center transition-all shadow-sm">
              <ArrowUp className="w-4 h-4" />
            </div>
          </button>
        </div>
      </div>
    </footer>
  );
};

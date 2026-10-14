import React, { useState, useEffect } from 'react';
import { MapPin, Phone, Calendar, Lock, ArrowRight, Menu, X, Sparkles } from 'lucide-react';

interface HeaderProps {
  onOpenModal: () => void;
  onOpenPortalModal?: (role?: 'student' | 'parent' | 'crm' | 'faculty') => void;
}

export const Header: React.FC<HeaderProps> = ({ onOpenModal, onOpenPortalModal }) => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [activeSection, setActiveSection] = useState('hero');

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 40);

      const sections = ['continuum', 'about', 'ceo-message', 'leadership', 'philosophy', 'why-us', 'success', 'campus', 'admissions', 'news'];
      for (const sectionId of sections) {
        const el = document.getElementById(sectionId);
        if (el) {
          const rect = el.getBoundingClientRect();
          if (rect.top <= 140 && rect.bottom >= 140) {
            setActiveSection(sectionId);
            break;
          }
        }
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = [
    { name: 'Continuum', href: '#continuum', id: 'continuum' },
    { name: 'LMS & CRM', href: '#lms-crm', id: 'lms-crm' },
    { name: 'CEO Vision', href: '#ceo-message', id: 'ceo-message' },
    { name: 'Directorate', href: '#leadership', id: 'leadership' },
    { name: 'Philosophy', href: '#philosophy', id: 'philosophy' },
    { name: 'Why GenZ', href: '#why-us', id: 'why-us' },
    { name: 'Placements', href: '#success', id: 'success' },
    { name: 'Admissions', href: '#admissions', id: 'admissions' },
  ];



  return (
    <>
      {/* Top Announcement Bar with Multi-Shade Logo Theme Glass Effects */}
      <div className="bg-gradient-to-r from-[#07172C] via-[#0B2545] to-[#07172C] text-white/90 text-xs py-2 border-b border-cyan/20 hidden md:block backdrop-blur-xl">
        <div className="max-w-7xl mx-auto px-6 flex justify-between items-center">
          <div className="flex items-center gap-3">
            {/* Shade 1: Electric Cyan Glass Pill */}
            <span
              onClick={onOpenModal}
              className="inline-flex items-center gap-2 bg-cyan/15 hover:bg-cyan/30 text-cyan-light font-bold px-3 py-1 rounded-full border border-cyan/30 hover:border-cyan/70 hover:shadow-[0_0_16px_rgba(0,153,255,0.45)] hover:scale-105 transition-all duration-300 text-[11px] backdrop-blur-md cursor-pointer"
            >
              <span className="w-2 h-2 rounded-full bg-cyan-light animate-ping" />
              Admissions Open 2026–27
            </span>

            {/* Shade 2: Sky Ice Blue Glass Pill */}
            <a
              href="#campus"
              className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 hover:bg-sky-400/20 text-white/80 hover:text-sky-200 border border-white/10 hover:border-sky-400/50 hover:shadow-[0_0_16px_rgba(56,189,248,0.35)] hover:scale-105 transition-all duration-300 text-[11px] backdrop-blur-md"
            >
              <MapPin className="w-3.5 h-3.5 text-cyan" />
              <span>Islamabad & Lahore Campuses</span>
            </a>
          </div>

          <div className="flex items-center gap-3">
            {/* Shade 3: Royal Navy & Cyan Glass Pill */}
            <button
              onClick={onOpenModal}
              className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 hover:bg-gradient-to-r hover:from-cyan/25 hover:to-blue-600/30 text-white/80 hover:text-cyan-light border border-white/10 hover:border-cyan/50 hover:shadow-[0_0_16px_rgba(0,153,255,0.4)] hover:scale-105 transition-all duration-300 text-[11px] backdrop-blur-md"
            >
              <Calendar className="w-3.5 h-3.5 text-cyan" />
              <span>Book Campus Walkthrough</span>
            </button>

            {/* Shade 4: Unified LMS & CRM Portal Glass Pill */}
            <button
              onClick={() => (onOpenPortalModal ? onOpenPortalModal('student') : window.location.assign('#lms-crm'))}
              className="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-cyan/20 hover:bg-gradient-to-r hover:from-cyan hover:to-blue-600 text-cyan-light hover:text-white border border-cyan/40 hover:border-white/40 hover:shadow-[0_0_20px_rgba(0,153,255,0.6)] hover:scale-105 transition-all duration-300 text-[11px] font-bold backdrop-blur-md group"
            >
              <Lock className="w-3.5 h-3.5 text-cyan group-hover:text-white transition-colors" />
              <span>LMS & CRM Gateway</span>
            </button>
          </div>
        </div>
      </div>

      {/* Main Sticky Navbar */}
      <nav
        className={`sticky top-0 z-50 transition-all duration-300 ${
          isScrolled
            ? 'bg-white/95 backdrop-blur-md shadow-md py-2 border-b border-navy/5'
            : 'bg-white/90 backdrop-blur-sm py-2.5 sm:py-3 border-b border-navy/5'
        }`}
      >
        <div className="max-w-7xl mx-auto px-6 flex items-center justify-between">
          {/* Brand Logo - Cleanly aligned on the left and neatly proportioned */}
          <a href="#" className="flex items-center -ml-1 sm:ml-0 group">
            <img
              src="/assets/images/genz-logo.png"
              alt="GenZ International School Logo"
              className="h-12 sm:h-14 md:h-16 w-auto max-h-[64px] object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-sm"
            />
          </a>

          {/* Desktop Navigation Links */}
          <div className="hidden lg:flex items-center gap-8">
            {navLinks.map((link) => (
              <a
                key={link.name}
                href={link.href}
                className={`font-display text-sm font-semibold tracking-wide transition-colors relative py-1 ${
                  activeSection === link.id
                    ? 'text-cyan'
                    : 'text-navy hover:text-cyan'
                }`}
              >
                {link.name}
                {activeSection === link.id && (
                  <span className="absolute bottom-0 left-0 w-full h-0.5 bg-cyan rounded-full" />
                )}
              </a>
            ))}
          </div>

          {/* Action CTAs */}
          <div className="hidden sm:flex items-center gap-3">
            <button
              onClick={() => (onOpenPortalModal ? onOpenPortalModal('student') : window.location.assign('#lms-crm'))}
              className="px-4 py-2 text-xs font-display font-semibold text-navy hover:text-cyan border border-navy/10 rounded-full hover:border-cyan transition-all flex items-center gap-1.5 cursor-pointer bg-slate-50/50 hover:bg-cyan/5"
            >
              <span className="w-1.5 h-1.5 rounded-full bg-cyan animate-ping" />
              <span>LMS / CRM Portal</span>
            </button>
            <button
              onClick={onOpenModal}
              className="inline-flex items-center gap-2 px-5 py-2.5 bg-navy text-white text-xs font-display font-bold uppercase tracking-wider rounded-full hover:bg-navy-dark hover:shadow-lg transition-all transform hover:-translate-y-0.5 group"
            >
              <span>Apply Now</span>
              <span className="w-2 h-2 rounded-full bg-cyan group-hover:scale-125 transition-transform" />
            </button>
          </div>

          {/* Mobile Menu Hamburger */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="lg:hidden p-2 text-navy hover:text-cyan transition-colors"
            aria-label="Toggle navigation menu"
          >
            {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>

        {/* Mobile Dropdown Drawer */}
        {mobileMenuOpen && (
          <div className="lg:hidden bg-white border-b border-navy/10 px-6 py-6 shadow-xl animate-in slide-in-from-top">
            <div className="flex flex-col gap-4">
              {navLinks.map((link) => (
                <a
                  key={link.name}
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`text-base font-display font-bold py-2 border-b border-navy/5 ${
                    activeSection === link.id ? 'text-cyan' : 'text-navy'
                  }`}
                >
                  {link.name}
                </a>
              ))}
              <div className="pt-4 flex flex-col gap-3">
                <button
                  onClick={() => {
                    setMobileMenuOpen(false);
                    onOpenModal();
                  }}
                  className="w-full py-3 bg-navy text-white text-center rounded-xl font-display font-bold text-sm shadow-md"
                >
                  Apply Now / Admissions
                </button>
                <button
                  onClick={() => {
                    setMobileMenuOpen(false);
                    if (onOpenPortalModal) onOpenPortalModal('student');
                  }}
                  className="w-full py-3 bg-cyan/10 text-cyan-dark border border-cyan/30 text-center rounded-xl font-display font-bold text-sm"
                >
                  Launch LMS & CRM Portal
                </button>
              </div>
            </div>
          </div>
        )}
      </nav>
    </>
  );
};

import React, { useState, useEffect } from 'react';
import { Calendar, ChevronUp } from 'lucide-react';

interface FloatingActionsProps {
  onOpenModal: () => void;
}

export const FloatingActions: React.FC<FloatingActionsProps> = ({ onOpenModal }) => {
  const [showBackToTop, setShowBackToTop] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setShowBackToTop(window.scrollY > 400);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <div className="fixed bottom-6 right-6 z-40 flex flex-col gap-3">
      {/* Booking Shortcut Button */}
      <button
        onClick={onOpenModal}
        className="w-12 h-12 rounded-full bg-cyan text-white shadow-xl hover:shadow-cyan-glow flex items-center justify-center hover:scale-110 active:scale-95 transition-all"
        aria-label="Book a campus walkthrough"
        title="Book a campus walkthrough"
      >
        <Calendar className="w-5 h-5" />
      </button>

      {/* Back To Top Button */}
      {showBackToTop && (
        <button
          onClick={scrollToTop}
          className="w-12 h-12 rounded-full bg-navy text-white shadow-xl flex items-center justify-center hover:bg-navy-dark hover:scale-110 active:scale-95 transition-all animate-in fade-in"
          aria-label="Scroll back to top"
          title="Scroll back to top"
        >
          <ChevronUp className="w-5 h-5" />
        </button>
      )}
    </div>
  );
};

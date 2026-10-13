import React from 'react';
import { Calendar, Download, Sparkles } from 'lucide-react';

interface CallToActionProps {
  onOpenModal: () => void;
}

export const CallToAction: React.FC<CallToActionProps> = ({ onOpenModal }) => {
  return (
    <section className="py-20 bg-white relative">
      <div className="max-w-7xl mx-auto px-6">
        <div className="rounded-3xl bg-gradient-to-br from-navy via-navy-light to-navy-dark p-10 sm:p-16 lg:p-20 text-white text-center relative overflow-hidden shadow-2xl border border-white/10">
          {/* Background Ambient Glows */}
          <div className="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-cyan/25 blur-3xl pointer-events-none" />
          <div className="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-cyan-light/20 blur-3xl pointer-events-none" />

          <div className="relative z-10 max-w-3xl mx-auto">
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-cyan-light text-xs font-display font-bold uppercase tracking-wider mb-6">
              <Sparkles className="w-3.5 h-3.5 text-cyan" />
              <span>Academic Session 2026–2027</span>
            </div>

            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.15] mb-6">
              Ready to Launch Your Child's Journey to Global Excellence?
            </h2>

            <p className="text-white/80 text-base sm:text-lg leading-relaxed mb-10 max-w-2xl mx-auto">
              Admissions are now open for Early Years, Primary, Middle, and Senior Cambridge streams across Islamabad and Lahore campuses.
            </p>

            <div className="flex flex-wrap items-center justify-center gap-4">
              <button
                onClick={onOpenModal}
                className="inline-flex items-center justify-center gap-3 px-8 py-4 bg-cyan text-white rounded-full font-display font-bold text-sm tracking-wide hover:bg-cyan-vibrant shadow-xl hover:shadow-cyan-glow transition-all transform hover:-translate-y-0.5"
              >
                <Calendar className="w-4 h-4" />
                <span>Schedule Private Campus Walkthrough</span>
              </button>

              <a
                href="/assets/images/genz-logo.png"
                download
                className="inline-flex items-center justify-center gap-2 px-7 py-4 bg-white/10 text-white hover:text-cyan-light border border-white/20 rounded-full font-display font-bold text-sm hover:border-cyan-light transition-all backdrop-blur-sm"
              >
                <Download className="w-4 h-4" />
                <span>Download 2026–27 Prospectus</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

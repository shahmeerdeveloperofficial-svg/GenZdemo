import React from 'react';
import { motion } from 'framer-motion';
import { Award, ArrowRight, ShieldCheck, Globe2, BookOpenCheck, Target } from 'lucide-react';

export const WelcomeSection: React.FC = () => {
  return (
    <section className="py-20 md:py-28 bg-white relative overflow-hidden" id="about">
      <div className="max-w-7xl mx-auto px-6">
        {/* Editorial Intro Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-16">
          <div className="lg:col-span-6">
            <div className="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-5">
              <Award className="w-3.5 h-3.5 text-cyan" />
              <span>About GenZ International</span>
            </div>
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy tracking-tight leading-[1.15]">
              A World-Class School Grounded in Intellectual Curiosity & Purpose<span className="heading-cursor-line">|</span>
            </h2>
          </div>

          <div className="lg:col-span-6 flex flex-col justify-between">
            <p className="text-navy text-lg sm:text-xl font-semibold leading-relaxed mb-4">
              GenZ International School was established to redefine secondary and foundational schooling — blending British Cambridge academic prestige with forward-thinking problem solving.
            </p>
            <p className="text-slate-600 text-base leading-relaxed mb-6">
              Our campus community brings together inquisitive minds, distinguished international educators, and a structured continuum from Early Years to A-Levels. We believe that true academic rigor is not about rote retention, but about the courage to question, collaborate, and create solutions that transcend borders.
            </p>
            <div>
              <a
                href="#philosophy"
                className="inline-flex items-center gap-2 text-cyan font-display font-bold text-sm hover:gap-3 transition-all"
              >
                <span>Read our Educational Philosophy</span>
                <ArrowRight className="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

        {/* 4 Feature Metric Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {/* Card 1 */}
          <motion.div
            className="p-8 rounded-3xl bg-cream border border-navy/5 hover:border-cyan hover:shadow-xl transition-all duration-300 group"
            whileHover={{ y: -6 }}
          >
            <div className="w-12 h-12 rounded-2xl bg-cyan/10 text-cyan flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <BookOpenCheck className="w-6 h-6" />
            </div>
            <div className="font-display text-4xl font-extrabold text-cyan mb-2">4</div>
            <h3 className="font-display text-lg font-bold text-navy mb-2">Articulated Programmes</h3>
            <p className="text-slate-600 text-xs leading-relaxed">
              Early Years, Primary, Middle, and Senior Cambridge seamlessly mapped for uninterrupted academic growth.
            </p>
          </motion.div>

          {/* Card 2 */}
          <motion.div
            className="p-8 rounded-3xl bg-cream border border-navy/5 hover:border-cyan hover:shadow-xl transition-all duration-300 group"
            whileHover={{ y: -6 }}
          >
            <div className="w-12 h-12 rounded-2xl bg-navy/10 text-navy flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <Globe2 className="w-6 h-6" />
            </div>
            <div className="font-display text-4xl font-extrabold text-navy mb-2">1,500+</div>
            <h3 className="font-display text-lg font-bold text-navy mb-2">Top 100 University Alumni</h3>
            <p className="text-slate-600 text-xs leading-relaxed">
              Scholars studying at Stanford, Oxford, Toronto, UCL, McGill, NUS, and University of Melbourne.
            </p>
          </motion.div>

          {/* Card 3 */}
          <motion.div
            className="p-8 rounded-3xl bg-cream border border-navy/5 hover:border-cyan hover:shadow-xl transition-all duration-300 group"
            whileHover={{ y: -6 }}
          >
            <div className="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <ShieldCheck className="w-6 h-6" />
            </div>
            <div className="font-display text-4xl font-extrabold text-gold mb-2">18+</div>
            <h3 className="font-display text-lg font-bold text-navy mb-2">Nationalities Represented</h3>
            <p className="text-slate-600 text-xs leading-relaxed">
              A genuinely cosmopolitan student body cultivating mutual respect, cultural fluency, and global perspective.
            </p>
          </motion.div>

          {/* Card 4 */}
          <motion.div
            className="p-8 rounded-3xl bg-cream border border-navy/5 hover:border-cyan hover:shadow-xl transition-all duration-300 group"
            whileHover={{ y: -6 }}
          >
            <div className="w-12 h-12 rounded-2xl bg-cyan/10 text-cyan flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <Target className="w-6 h-6" />
            </div>
            <div className="font-display text-4xl font-extrabold text-cyan mb-2">100%</div>
            <h3 className="font-display text-lg font-bold text-navy mb-2">Advisory Guidance</h3>
            <p className="text-slate-600 text-xs leading-relaxed">
              Full-time college counseling ensuring every scholar receives tailored guidance and scholarship advocacy.
            </p>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

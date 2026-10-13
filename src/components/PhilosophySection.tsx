import React from 'react';
import { motion } from 'framer-motion';
import { Compass, Lightbulb, Users, Shield, Cpu } from 'lucide-react';

export const PhilosophySection: React.FC = () => {
  const pillars = [
    {
      Icon: Lightbulb,
      title: 'Intellectual Inquiry Over Memorization',
      desc: 'We cultivate deep conceptual understanding through inquiry-based models where students hypothesize, analyze, debate, and synthesize evidence.'
    },
    {
      Icon: Cpu,
      title: 'Technological & AI Fluency',
      desc: 'Integrating computational thinking, autonomous robotics, and machine-learning literacy from junior school so scholars become creators of technology, not mere consumers.'
    },
    {
      Icon: Users,
      title: 'Empathy & Global Citizenship',
      desc: 'Fostering emotional intelligence, multilingual communication, and international perspective to nurture compassionate, responsible leaders of tomorrow.'
    },
    {
      Icon: Shield,
      title: 'Ethical Leadership & Character',
      desc: 'Anchored by the ethos of Knowledge, Action, and Resolve. Character development and resilience are given equal priority to examination laurels.'
    }
  ];

  return (
    <section className="py-24 bg-gradient-to-b from-white via-cyan-subtle/25 to-white relative" id="philosophy">
      <div className="max-w-7xl mx-auto px-6">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4">
            <Compass className="w-3.5 h-3.5" />
            <span>Our Educational Ethos</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy tracking-tight leading-[1.15] mb-6">
            Four Foundational Pillars of the GenZ Learning Journey
          </h2>
          <p className="text-slate-600 text-base sm:text-lg">
            A cohesive approach ensuring academic mastery, technological capability, ethical conviction, and worldwide mobility.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {pillars.map((pillar, idx) => (
            <motion.div
              key={pillar.title}
              className="p-8 sm:p-10 rounded-3xl bg-white border border-navy/5 shadow-glass hover:shadow-2xl hover:border-cyan/40 hover:-translate-y-1.5 transition-all duration-300 flex items-start gap-6 group cursor-pointer"
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1, duration: 0.5 }}
            >
              {/* Premium Interactive Icon Box with Luminous White Icon on Hover */}
              <div className="w-14 h-14 rounded-2xl bg-cyan/10 border border-cyan/20 flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-cyan group-hover:to-navy shadow-sm group-hover:shadow-cyan-glow transition-all duration-300">
                <pillar.Icon className="w-6 h-6 text-cyan group-hover:text-white group-hover:scale-110 transition-all duration-300" />
              </div>
              <div>
                <h3 className="font-display text-xl font-bold text-navy mb-3 group-hover:text-cyan transition-colors">
                  {pillar.title}
                </h3>
                <p className="text-slate-600 text-sm leading-relaxed">
                  {pillar.desc}
                </p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

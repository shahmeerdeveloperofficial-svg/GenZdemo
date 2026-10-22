import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles, BookOpen, Cpu, Plane, LayoutDashboard } from 'lucide-react';

export const WhyUsSection: React.FC = () => {
  const cards = getContent("sections.benefits.items", [
  {
    "Icon": "BookOpen",
    "title": "Complete Cambridge & Global Continuum",
    "desc": "Seamless progression from early years through IGCSE and A Levels, eliminating academic disruption and setting an unbroken trajectory towards university readiness."
  },
  {
    "Icon": "Cpu",
    "title": "Future-Ready STEM & AI Innovation Hub",
    "desc": "Coding, autonomous robotics, digital fabrication, and ethical AI labs integrated from Grade 3, preparing scholars to build and lead in a technology-first era."
  },
  {
    "Icon": "Plane",
    "title": "The Global Flight Path Advisory",
    "desc": "Dedicated in-house counseling wing offering SAT/IELTS preparation, masterclass essay workshops, and Ivy League & Russell Group full-scholarship applications."
  },
  {
    "Icon": "LayoutDashboard",
    "title": "Real-Time Parent & Scholar Digital Gateway",
    "desc": "A secure 24/7 web portal providing transparent, live tracking of attendance, curriculum milestones, rubric feedback, and individual portfolio showcases."
  }
]).map(item => ({...item, Icon: ({BookOpen,Cpu,Plane,LayoutDashboard} as Record<string, typeof BookOpen>)[item.Icon] || BookOpen}));

  return (
    <section className="py-28 bg-gradient-to-b from-white via-cyan-subtle/20 to-white relative overflow-hidden" id="why-us">
      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-20">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4">
            <Sparkles className="w-3.5 h-3.5" />
            <span>{getContent<string>("sections.benefits.copy.theGenzAdvantage", "The GenZ Advantage")}</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy tracking-tight leading-[1.15] mb-6">{getContent<string>("sections.benefits.copy.whatSetsGenzInternationalApart", "What Sets GenZ International Apart")}<span className="heading-cursor-line">{getContent<string>("sections.benefits.copy.label", "|")}</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg">{getContent<string>("sections.benefits.copy.purposeBuiltPedagogyAndStrategicGlobal", "Purpose-built pedagogy and strategic global preparation designed to unleash each scholar’s highest potential.")}</p>
        </div>

        {/* Staggered Grid with Animated Connecting SVG Line */}
        <div className="relative max-w-5xl mx-auto">
          {/* Animated Connecting SVG Line */}
          <svg
            className="hidden lg:block absolute inset-0 w-full h-full pointer-events-none z-0"
            viewBox="0 0 1000 700"
            fill="none"
            preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M 250 160 Q 500 160 500 350 T 750 540"
              stroke="rgba(0, 153, 255, 0.15)"
              strokeWidth="2"
            />
            <path
              className="webflow-animated-line"
              d="M 250 160 Q 500 160 500 350 T 750 540"
              stroke="#0099FF"
              strokeWidth="3"
            />
          </svg>

          {/* 4 Cards Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 relative z-10">
            {cards.map((card, idx) => (
              <motion.div
                key={card.title}
                className="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-10 border border-navy/5 shadow-glass hover:shadow-2xl hover:border-cyan/40 hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer"
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.15, duration: 0.6 }}
              >
                {/* Luminous White Icon on Hover */}
                <div className="w-14 h-14 rounded-2xl bg-cyan/10 border border-cyan/20 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-cyan group-hover:to-navy shadow-sm group-hover:shadow-cyan-glow transition-all duration-300">
                  <card.Icon className="w-7 h-7 text-cyan group-hover:text-white group-hover:scale-110 transition-all duration-300" />
                </div>
                <h3 className="font-display text-xl font-bold text-navy mb-3 group-hover:text-cyan transition-colors">
                  {card.title}
                </h3>
                <p className="text-slate-600 text-sm leading-relaxed">
                  {card.desc}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

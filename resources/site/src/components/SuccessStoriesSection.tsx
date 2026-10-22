import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React from 'react';
import { motion } from 'framer-motion';
import { Globe2, Sparkles, Award } from 'lucide-react';
import { SUCCESS_STORIES } from '../data/schoolData';

export const SuccessStoriesSection: React.FC = () => {
  return (
    <section className="py-28 bg-navy-dark text-white relative overflow-hidden" id="success">
      {/* Background Radial Glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[550px] bg-cyan/15 blur-3xl transform-gpu pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Header with Blinking Cursor */}
        <div className="text-center max-w-3xl mx-auto mb-20">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-cyan-light text-xs font-display font-bold uppercase tracking-wider mb-4">
            <Globe2 className="w-3.5 h-3.5 text-cyan" />
            <span>{getContent<string>("sections.stories.copy.successStories", "( Success Stories )")}</span>
          </div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.1] mb-6">{getContent<string>("sections.stories.copy.fromGenzToTheWorld", "From GenZ to the World")}<span className="heading-cursor-line">{getContent<string>("sections.stories.copy.label", "|")}</span>
          </h2>
          <p className="text-white/70 text-base sm:text-lg">{getContent<string>("sections.stories.copy.ourScholarsContinueToExcelAt", "Our scholars continue to excel at globally recognized institutions, securing over $12M in full fellowships and honors worldwide.")}</p>
        </div>

        {/* 4 Interactive Success Cards with LA International Style Vertical Photo Reveal */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {!SUCCESS_STORIES.length && <p className="col-span-full text-white/70 text-center py-10">{getContent('sections.stories.copy.emptyMessage', 'Student stories will appear here when published.')}</p>}
          {SUCCESS_STORIES.map((story, idx) => (
            <motion.div
              key={story.id}
              className="rounded-3xl p-7 bg-white/[0.06] border border-white/10 hover:border-cyan hover:bg-white/[0.12] transition-all duration-500 flex flex-col justify-between group relative overflow-hidden min-h-[440px]"
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1, duration: 0.6 }}
            >
              {/* Photo Reveal Overlay on Hover (LA International signature effect!) */}
              <div className="absolute inset-0 z-0 h-0 group-hover:h-full transition-all duration-500 ease-out overflow-hidden pointer-events-none">
                <img loading="lazy" decoding="async"
                  src={siteAsset(story.avatar)}
                  alt={story.studentName}
                  className="w-full h-full object-cover opacity-20 filter contrast-125"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-navy-dark via-navy-dark/90 to-transparent" />
              </div>

              {/* Card Content Top */}
              <div className="relative z-10">
                <div className="flex items-center gap-4 mb-6">
                  <img loading="lazy" decoding="async"
                    src={siteAsset(story.avatar)}
                    alt={story.studentName}
                    className="w-16 h-16 rounded-full object-cover border-2 border-cyan shadow-lg group-hover:scale-110 transition-transform duration-300"
                  />
                  <div>
                    <h3 className="font-display font-extrabold text-xl text-white group-hover:text-cyan-light transition-colors">
                      {story.studentName}
                    </h3>
                    <div className="text-xs text-cyan-light font-semibold">
                      {story.batch}
                    </div>
                  </div>
                </div>

                {(story.university || story.program || story.scholarshipTier) && <div className="p-4 rounded-2xl bg-white/[0.06] border border-white/10 mb-6 backdrop-blur-sm">
                  {story.country && <div className="text-[10px] uppercase font-bold text-white/50 tracking-wider mb-1">{story.country}</div>}
                  {story.university && <div className="font-display text-lg font-black text-white mb-1">{story.university}</div>}
                  {story.program && <div className="text-xs text-white/80 font-medium mb-3">{story.program}</div>}
                  {story.scholarshipTier && <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold/20 text-gold text-[11px] font-bold border border-gold/30"><Sparkles className="w-3 h-3" /><span>{story.scholarshipTier}</span></div>}
                </div>}
              </div>

              {/* Card Content Bottom */}
              <div className="relative z-10 pt-4 border-t border-white/10">
                <p className="text-xs text-white/80 italic leading-relaxed">{getContent<string>("sections.stories.copy.label3", "\"")}{story.quote}{getContent<string>("sections.stories.copy.label3", "\"")}</p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

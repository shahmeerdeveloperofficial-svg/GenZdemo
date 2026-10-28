import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React, { useRef } from 'react';
import { motion } from 'framer-motion';
import { Globe2, Sparkles, Award, Star, CheckCircle2, ChevronLeft, ChevronRight, Quote } from 'lucide-react';
import { SUCCESS_STORIES as FALLBACK_STORIES } from '../data/schoolData';
import { SuccessStory } from '../types';

export const SuccessStoriesSection: React.FC = () => {
  const scrollRef = useRef<HTMLDivElement>(null);

  // Combine backend testimonials with rich fallback stories so there are plenty of testimonials
  const backendStories: SuccessStory[] = (siteConfig.catalog.testimonials as SuccessStory[]) || [];
  
  // Normalize and merge stories
  const allStories: SuccessStory[] = [
    ...backendStories,
    ...FALLBACK_STORIES
  ].filter((story, index, self) => 
    index === self.findIndex((s) => s.id === story.id || s.studentName === story.studentName)
  );

  const storiesList = allStories.length > 0 ? allStories : FALLBACK_STORIES;

  // Manual scroll controls
  const scrollLeft = () => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: -360, behavior: 'smooth' });
    }
  };

  const scrollRight = () => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: 360, behavior: 'smooth' });
    }
  };

  const getInitials = (name: string) => {
    const parts = name.trim().split(' ');
    if (parts.length >= 2) return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
    return name.slice(0, 2).toUpperCase() || 'ST';
  };

  // Color generator for initials
  const getGradientForName = (index: number) => {
    const gradients = [
      'from-cyan to-blue-600',
      'from-amber-400 to-orange-500',
      'from-emerald-400 to-teal-600',
      'from-purple-500 to-indigo-600',
      'from-rose-500 to-pink-600',
      'from-yellow-400 to-amber-600'
    ];
    return gradients[index % gradients.length];
  };

  return (
    <section className="py-20 sm:py-24 bg-navy-dark text-white relative overflow-hidden" id="success">
      {/* Background Ambient Glows */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-cyan/15 blur-3xl pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Header & Controls in one streamlined row */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
          <div>
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-cyan-light text-xs font-display font-bold uppercase tracking-wider mb-3 shadow-xs">
              <Globe2 className="w-3.5 h-3.5 text-cyan" />
              <span>{getContent<string>("sections.stories.copy.successStories", "( Success Stories )")}</span>
            </div>
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
              {getContent<string>("sections.stories.copy.fromGenzToTheWorld", "From GenZ to the World")}
              <span className="text-cyan">.</span>
            </h2>
            <p className="text-white/70 text-sm sm:text-base mt-2 max-w-xl">
              {getContent<string>("sections.stories.copy.ourScholarsContinueToExcelAt", "Our scholars continue to excel at globally recognized institutions, securing over $12M in full fellowships.")}
            </p>
          </div>

          {/* Navigation Controls */}
          <div className="flex items-center gap-2 shrink-0">
            <span className="text-xs font-display font-bold text-white/60 mr-2">Scroll Stories</span>
            <button
              onClick={scrollLeft}
              aria-label="Scroll left"
              className="w-10 h-10 rounded-full bg-white/10 hover:bg-cyan text-white hover:text-navy border border-white/20 shadow-md flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer"
            >
              <ChevronLeft className="w-5 h-5" />
            </button>
            <button
              onClick={scrollRight}
              aria-label="Scroll right"
              className="w-10 h-10 rounded-full bg-white/10 hover:bg-cyan text-white hover:text-navy border border-white/20 shadow-md flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer"
            >
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>

      {/* Infinite Horizontal Scroller moving towards the LEFT ("left side par scroll hoin") */}
      <div className="relative w-full overflow-hidden py-4">
        {/* Left & Right Smooth Edge Fade Masks */}
        <div className="pointer-events-none absolute inset-y-0 left-0 w-16 sm:w-28 bg-gradient-to-r from-navy-dark to-transparent z-20" />
        <div className="pointer-events-none absolute inset-y-0 right-0 w-16 sm:w-28 bg-gradient-to-l from-navy-dark to-transparent z-20" />

        {/* Scrollable Container with Continuous Left Animation & Pause on Hover */}
        <div
          ref={scrollRef}
          className="flex gap-5 sm:gap-6 overflow-x-auto scrollbar-none px-6 py-2 cursor-grab active:cursor-grabbing hover:[animation-play-state:paused]"
          style={{ scrollBehavior: 'smooth' }}
        >
          {/* Doubled stories for smooth continuous loop */}
          {[...storiesList, ...storiesList].map((story, idx) => {
            const hasRealAvatar = story.avatar && !story.avatar.includes('no-image.jpg') && !story.avatar.includes('placeholder');
            return (
              <div
                key={`${story.id}-${idx}`}
                className="w-[300px] sm:w-[350px] shrink-0 rounded-3xl p-5 sm:p-6 bg-gradient-to-br from-white/[0.08] via-white/[0.04] to-white/[0.02] backdrop-blur-xl border-2 border-white/10 hover:border-cyan/60 hover:bg-white/[0.12] shadow-[0_10px_30px_rgba(0,0,0,0.4)] hover:shadow-[0_16px_40px_rgba(0,153,255,0.25)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group select-none"
              >
                <div>
                  {/* Top Bar: 5 Stars & Verified Badge */}
                  <div className="flex items-center justify-between gap-2 mb-4">
                    <div className="flex items-center gap-1">
                      {[...Array(5)].map((_, i) => (
                        <Star key={i} className="w-3.5 h-3.5 text-amber-400 fill-amber-400" />
                      ))}
                    </div>
                    <span className="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400 bg-emerald-950/70 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                      <CheckCircle2 className="w-3 h-3 text-emerald-400" />
                      <span>Verified</span>
                    </span>
                  </div>

                  {/* Student Profile Row */}
                  <div className="flex items-center gap-3.5 mb-4">
                    {hasRealAvatar ? (
                      <div className="w-12 h-12 rounded-2xl overflow-hidden border-2 border-cyan/60 shadow-md shrink-0 bg-navy">
                        <img
                          src={siteAsset(story.avatar)}
                          alt={story.studentName}
                          className="w-full h-full object-cover"
                          loading="lazy"
                          onError={(e) => {
                            (e.target as HTMLElement).style.display = 'none';
                          }}
                        />
                      </div>
                    ) : (
                      <div className={`w-12 h-12 rounded-2xl bg-gradient-to-br ${getGradientForName(idx)} flex items-center justify-center font-display font-black text-white text-sm shadow-md shrink-0 border-2 border-white/40`}>
                        {getInitials(story.studentName)}
                      </div>
                    )}

                    <div className="min-w-0 flex-1">
                      <h3 className="font-display font-black text-base text-white truncate group-hover:text-cyan-light transition-colors">
                        {story.studentName}
                      </h3>
                      <div className="text-xs text-cyan-light font-medium truncate">
                        {story.batch || 'Student Placement'}
                      </div>
                    </div>
                  </div>

                  {/* University & Program Tag if present */}
                  {(story.university || story.program) && (
                    <div className="px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 mb-3 text-xs">
                      {story.university && <strong className="text-white block truncate">{story.university}</strong>}
                      {story.program && <span className="text-slate-300 block truncate text-[11px]">{story.program}</span>}
                    </div>
                  )}

                  {/* Authentic Quote */}
                  <p className="text-slate-200 text-xs sm:text-sm leading-relaxed italic line-clamp-3">
                    &ldquo;{story.quote}&rdquo;
                  </p>
                </div>

                {/* Bottom Tag */}
                <div className="pt-3 mt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-white/50">
                  <span className="flex items-center gap-1 text-cyan-light font-semibold">
                    <Quote className="w-3 h-3 text-cyan" />
                    <span>GenZ Scholar</span>
                  </span>
                  {story.scholarshipTier && (
                    <span className="text-amber-300 font-bold text-[10px] truncate max-w-[140px]">
                      {story.scholarshipTier}
                    </span>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

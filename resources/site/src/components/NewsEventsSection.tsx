import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React from 'react';
import { motion } from 'framer-motion';
import { Bell, Calendar, MapPin, ArrowRight } from 'lucide-react';
import { NEWS_EVENTS } from '../data/schoolData';

interface NewsProps {
  onOpenModal: () => void;
}

export const NewsEventsSection: React.FC<NewsProps> = ({ onOpenModal }) => {
  return (
    <section className="py-28 bg-white relative" id="news">
      <div className="max-w-7xl mx-auto px-6">
        {/* Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
          <div>
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4">
              <Bell className="w-3.5 h-3.5" />
              <span>{getContent<string>("sections.news.copy.colloquiumsCampusNews", "Colloquiums & Campus News")}</span>
            </div>
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy tracking-tight leading-[1.15]">{getContent<string>("sections.news.copy.upcomingEventsAcademicMilestones", "Upcoming Events & Academic Milestones")}</h2>
          </div>
          <button
            onClick={() => window.location.assign(route('events'))}
            className="inline-flex items-center gap-2 px-6 py-3 border border-navy/15 rounded-full font-display font-bold text-xs uppercase tracking-wider text-navy hover:text-cyan hover:border-cyan transition-colors"
          >
            <span>{getContent<string>("sections.news.copy.reserveColloquiumSeat", "View all events")}</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </div>

        {/* 3 News Articles */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {!NEWS_EVENTS.length && <p className="col-span-full text-slate-600 text-center py-10">{getContent('sections.news.copy.emptyMessage', 'News and events will appear here when published.')}</p>}
          {NEWS_EVENTS.map((item, idx) => (
            <motion.article
              key={item.id}
              className="rounded-3xl overflow-hidden bg-cream border border-navy/10 shadow-glass hover:shadow-xl transition-all duration-300 flex flex-col group"
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.15, duration: 0.5 }}
            >
              <div className="relative h-56 overflow-hidden bg-navy-dark">
                <img loading="lazy" decoding="async"
                  src={siteAsset(item.image)}
                  alt={item.title}
                  className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                />
                <div className="absolute top-4 left-4 bg-navy/85 backdrop-blur-md px-3.5 py-1 rounded-full text-xs font-bold text-cyan-light border border-white/10 uppercase tracking-wider">
                  {item.category}
                </div>
              </div>

              <div className="p-8 flex-1 flex flex-col justify-between">
                <div>
                  <div className="flex items-center gap-2 text-xs font-medium text-slate-500 mb-3">
                    <Calendar className="w-3.5 h-3.5 text-cyan" />
                    <span>{item.date}</span>
                  </div>
                  <h3 className="font-display text-xl font-bold text-navy mb-3 group-hover:text-cyan transition-colors leading-snug">
                    {item.title}
                  </h3>
                  <p className="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6">
                    {item.excerpt}
                  </p>
                </div>

                <div className="pt-4 border-t border-navy/10 flex items-center justify-between">
                  <span className="text-[11px] text-slate-400 font-medium">
                    {item.location}
                  </span>
                  <button
                    onClick={() => item.url ? window.location.assign(safeUrl(item.url, route('events'))) : document.dispatchEvent(new CustomEvent('genz:inquiry', { detail: { event: item.title } }))}
                    className="inline-flex items-center gap-1.5 text-xs font-display font-bold text-cyan hover:text-navy transition-colors"
                  >
                    <span>{getContent<string>("sections.news.copy.rsvp", "Read more")}</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </motion.article>
          ))}
        </div>
      </div>
    </section>
  );
};

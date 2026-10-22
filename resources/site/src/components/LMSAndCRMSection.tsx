import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { GraduationCap, Database, BookOpen, CheckCircle2, ArrowRight, Sparkles, Lock } from 'lucide-react';
import { getContent, route } from '../content';

interface LMSCRMProps { onOpenPortalModal: (role: 'student' | 'parent' | 'crm' | 'faculty') => void; }
interface PortalFeature { title: string; description: string; }
interface PortalTab { id: string; label: string; tag: string; heading: string; description: string; action: string; features: PortalFeature[]; }

export const LMSAndCRMSection: React.FC<LMSCRMProps> = () => {
  const tabs = getContent<PortalTab[]>('sections.platform.tabs', []);
  const [activeId, setActiveId] = useState(tabs[0]?.id ?? '');
  const current = tabs.find(tab => tab.id === activeId) ?? tabs[0];
  const icons = [GraduationCap, Database, BookOpen];
  return (
    <section className="py-24 sm:py-32 bg-gradient-to-b from-[#FAF7F2] via-white to-[#F0F7FF] relative overflow-hidden" id="lms-crm">
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#0099ff0a_1px,transparent_1px),linear-gradient(to_bottom,#0099ff0a_1px,transparent_1px)] bg-[size:3rem_3rem] pointer-events-none" />
      <div className="max-w-7xl mx-auto px-6 relative z-10">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/15 border border-cyan/30 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4"><Sparkles className="w-3.5 h-3.5" /><span>{getContent('sections.platform.copy.badge', 'Learning & Admissions')}</span></div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">{getContent('sections.platform.copy.heading', 'Your learning journey, connected')}</h2>
          <p className="text-slate-600 text-base sm:text-lg leading-relaxed">{getContent('sections.platform.copy.description', 'Access your courses, documents and admissions support through the GenZ portal.')}</p>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-10" role="tablist" aria-label={getContent('sections.platform.copy.tabLabel', 'Portal features')}>
          {tabs.map((tab, index) => {const Icon = icons[index % icons.length]; return (
            <button key={tab.id} role="tab" id={`portal-tab-${tab.id}`} aria-selected={current?.id === tab.id} aria-controls="portal-feature-panel" onClick={() => setActiveId(tab.id)} className={`flex items-center gap-3 px-6 py-4 rounded-2xl text-left font-display transition-all ${current?.id === tab.id ? 'bg-navy text-white ring-2 ring-cyan/50 shadow-xl' : 'bg-white text-navy border border-navy/10'}`}>
              <Icon className="w-6 h-6 text-cyan shrink-0" /><span><strong className="block text-sm">{tab.label}</strong><span className={`text-xs ${current?.id === tab.id ? 'text-cyan-light' : 'text-slate-500'}`}>{tab.tag}</span></span>
            </button>
          )})}
        </div>
        <div className="bg-gradient-to-br from-[#07172C] to-[#0B2545] rounded-3xl border border-cyan/20 p-6 sm:p-10 shadow-2xl text-white">
          <AnimatePresence mode="wait">{current && (
            <motion.div key={current.id} id="portal-feature-panel" role="tabpanel" aria-labelledby={`portal-tab-${current.id}`} initial={{opacity:0,y:12}} animate={{opacity:1,y:0}} exit={{opacity:0}} className="space-y-6">
              <div><p className="text-cyan-light font-bold text-xs uppercase tracking-wider mb-2">{current.tag}</p><h3 className="font-display text-2xl sm:text-3xl font-bold">{current.heading}</h3><p className="text-white/70 mt-3 max-w-3xl">{current.description}</p></div>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">{current.features.map((feature,index) => <div key={`${feature.title}-${index}`} className="p-6 rounded-2xl bg-white/5 border border-white/10"><CheckCircle2 className="text-cyan w-6 h-6 mb-4" /><h4 className="font-bold mb-2">{feature.title}</h4><p className="text-white/70 text-sm leading-relaxed">{feature.description}</p></div>)}</div>
              <a href={route('login')} className="inline-flex items-center gap-2 bg-cyan text-navy font-bold px-7 py-3 rounded-full hover:bg-cyan-light">{current.action}<ArrowRight className="w-4 h-4" /></a>
            </motion.div>
          )}</AnimatePresence>
          <div className="mt-8 pt-6 border-t border-white/10 flex flex-col sm:flex-row gap-4 justify-between items-center"><p className="flex gap-2 items-center text-xs text-white/70"><Lock className="w-4 h-4 text-cyan" />{getContent('sections.platform.copy.accessNote', 'Your account determines which portal and records you can access.')}</p><a href={route('registration')} className="text-cyan-light text-sm font-bold hover:text-white">{getContent('sections.platform.copy.registrationAction', 'Start student registration')} <span aria-hidden="true">→</span></a></div>
        </div>
      </div>
    </section>
  );
};

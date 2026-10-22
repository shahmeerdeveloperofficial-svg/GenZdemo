import React, { useEffect, useRef, useState } from 'react';
import { X, CheckCircle, Send } from 'lucide-react';
import { getContent, postSiteForm, route, siteConfig, SiteFormError, siteText } from '../content';

interface InquiryModalProps { isOpen: boolean; onClose: () => void; event?: string }
const emptyForm = {student_name:'',parent_name:'',email:'',mobile:'',program:'',branch:'',message:'',website:''};

export const InquiryModal: React.FC<InquiryModalProps> = ({isOpen,onClose,event}) => {
  const [form,setForm]=useState(emptyForm);
  const [busy,setBusy]=useState(false);
  const [success,setSuccess]=useState('');
  const [error,setError]=useState('');
  const [errors,setErrors]=useState<Record<string,string[]>>({});
  const closeRef=useRef<HTMLButtonElement>(null);
  const branches=siteConfig.registration?.branches || [];
  const programs=getContent<string[]>('inquiry.programs',[]);
  useEffect(()=>{
    if(!isOpen) return;
    const previous=document.activeElement as HTMLElement|null;
    closeRef.current?.focus();
    const listener=(e:KeyboardEvent)=>{if(e.key==='Escape'&&!busy) onClose();};
    document.addEventListener('keydown',listener);
    return()=>{document.removeEventListener('keydown',listener);previous?.focus();};
  },[isOpen,onClose,busy]);
  if(!isOpen) return null;
  const fieldClass="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white text-navy focus:outline-none focus:ring-2 focus:ring-cyan/25 focus:border-cyan";
  const update=(key:keyof typeof form,value:string)=>setForm(current=>({...current,[key]:value}));
  const close=()=>{if(busy)return;setSuccess('');setError('');setErrors({});onClose();};
  const submit=async(e:React.FormEvent)=>{
    e.preventDefault();if(busy)return;setBusy(true);setError('');setErrors({});
    try {
      const result=await postSiteForm('inquiry',{...form,...(event?{event}:{})});
      setSuccess(siteText('inquiry','successMessage','').trim()||result.message||'Your inquiry has been saved.');
      setForm(emptyForm);
    } catch(err) {
      setError(err instanceof Error?err.message:'Unable to submit. Please try again.');
      if(err instanceof SiteFormError)setErrors(err.errors);
    } finally {setBusy(false);}
  };
  const fields: {key:keyof typeof form;label:string;type:string;required:boolean;max:number}[]=[
    {key:'student_name',label:'studentLabel',type:'text',required:true,max:180},
    {key:'parent_name',label:'parentLabel',type:'text',required:false,max:180},
    {key:'email',label:'emailLabel',type:'email',required:true,max:190},
    {key:'mobile',label:'mobileLabel',type:'tel',required:true,max:60},
  ];
  return <div className="fixed inset-0 z-[70] p-3 sm:p-6 flex items-center justify-center bg-navy-dark/85 backdrop-blur-md" role="dialog" aria-modal="true" aria-labelledby="inquiry-title" data-lenis-prevent onClick={close}>
    <div className="w-full max-w-2xl max-h-[90dvh] overflow-y-auto bg-white rounded-3xl shadow-2xl" onClick={e=>e.stopPropagation()}>
      <div className="bg-navy text-white p-6 sm:p-8 flex justify-between items-start gap-4">
        <div><p className="text-cyan-light text-xs uppercase tracking-wider mb-2">{siteText('inquiry','subtitle','Admission inquiry')}</p><h2 id="inquiry-title" className="font-display text-2xl font-bold">{siteText('inquiry','title','Talk to our admissions team')}</h2></div>
        <button type="button" ref={closeRef} onClick={close} disabled={busy} aria-label={siteText('inquiry','closeLabel','Close')} className="rounded-full p-2 hover:bg-white/15"><X size={20}/></button>
      </div>
      <div className="p-6 sm:p-8">
        {success ? <div className="text-center py-7" role="status">
          <CheckCircle className="w-14 h-14 text-emerald-600 mx-auto mb-5"/>
          <h3 className="font-display text-2xl font-bold mb-3">{siteText('inquiry','successTitle','Inquiry received')}</h3>
          <p className="text-slate-600 text-sm mb-7">{success}</p>
          <button onClick={close} className="bg-navy text-white rounded-xl px-6 py-3">{siteText('inquiry','returnLabel','Return to website')}</button>
        </div> : <form onSubmit={submit}>
          <p className="text-sm text-slate-600 mb-5">{siteText('inquiry','description','Share your details and our team will follow up.')}</p>
          {event&&<p className="mb-4 rounded-xl bg-cyan/10 p-3 text-sm">{siteText('inquiry','eventLabel','Regarding')}: {event}</p>}
          {error&&<p className="mb-5 rounded-xl bg-red-50 text-red-700 p-3 text-sm" role="alert">{error}</p>}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {fields.map(({key,label,type,required,max})=><div key={key}><label htmlFor={`inquiry-${key}`} className="block text-xs font-semibold mb-2">{siteText('inquiry',label,key)}{required?' *':''}</label><input id={`inquiry-${key}`} name={key} type={type} required={required} maxLength={max} minLength={key==='mobile'?6:undefined} autoComplete={key==='email'?'email':key==='mobile'?'tel':key==='student_name'?'name':undefined} value={form[key]} onChange={e=>update(key,e.target.value)} className={fieldClass} aria-invalid={!!errors[key]}/>{errors[key]&&<span className="text-red-600 text-xs">{errors[key][0]}</span>}</div>)}
            {([{key:'program',label:'programLabel',options:programs},{key:'branch',label:'branchLabel',options:branches}] as const).map(({key,label,options})=><div key={key}><label htmlFor={`inquiry-${key}`} className="block text-xs font-semibold mb-2">{siteText('inquiry',label,key)}</label><select id={`inquiry-${key}`} name={key} value={form[key]} onChange={e=>update(key,e.target.value)} className={fieldClass}><option value="">{siteText('inquiry','selectLabel','Please select')}</option>{options.map(value=><option key={value} value={value}>{value}</option>)}</select></div>)}
          </div>
          <label htmlFor="inquiry-message" className="block text-xs font-semibold mt-4 mb-2">{siteText('inquiry','messageLabel','Your message')}</label>
          <textarea id="inquiry-message" name="message" value={form.message} onChange={e=>update('message',e.target.value)} maxLength={5000} rows={3} className={fieldClass}/>
          <div className="absolute -left-[10000px] w-px h-px overflow-hidden" aria-hidden="true"><label>Website<input name="website" value={form.website} onChange={e=>update('website',e.target.value)} tabIndex={-1} autoComplete="off"/></label></div>
          <button disabled={busy} className="w-full mt-5 py-3.5 bg-navy hover:bg-cyan disabled:opacity-60 text-white rounded-xl font-semibold flex items-center justify-center gap-2"><Send size={17}/>{siteText('inquiry',busy?'busyLabel':'submitLabel',busy?'Sending…':'Send inquiry')}</button>
          <p className="text-center text-xs text-slate-500 mt-5">{siteText('inquiry','registrationPrompt','Ready to register?')} <a href={route('registration')} className="text-cyan font-semibold underline">{siteText('inquiry','registrationLabel','Open registration form')}</a></p>
        </form>}
      </div>
    </div>
  </div>;
};

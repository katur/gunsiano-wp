/*1355779399,173217045*/

if (self.CavalryLogger) { CavalryLogger.start_js(["5PZ3q"]); }

__d("TypeaheadExcludeBootstrapFromQueryCache",["copyProperties"],function(a,b,c,d,e,f){var g=b('copyProperties');function h(i){this._data=i.getData();}g(h.prototype,{enable:function(){this._buildingQueryCache=false;this._buildQueryCache=this._data.subscribe('buildQueryCache',function(){this._buildingQueryCache=true;}.bind(this));this._mergeUids=this._data.subscribe('mergeUids',function(i,j){if(this._buildingQueryCache)j.local_uids.splice(0,j.local_uids.length);}.bind(this));this._fetchComplete=this._data.subscribe('fetchComplete',function(){this._buildingQueryCache=false;}.bind(this));},disable:function(){this._data.unsubscribe(this._buildQueryCache);this._data.unsubscribe(this._mergeUids);this._data.unsubscribe(this._fetchComplete);}});e.exports=h;});
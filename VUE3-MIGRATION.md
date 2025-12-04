# Petite Vue to Vue 3 Options API Migration Guide

## Why Vue 3 Options API? (MINIMAL CHANGES)

**Convert from Petite Vue to Vue 3 Options API with MOST minimal code changes.**:
- ✅ Already using `createApp()` - just change import from `"petite-vue"` → `"vue"`
- ✅ No need to use `setup()` or `watch()` or composition api
- ✅ Simple `data()` function returns plain objects 
- ✅ Methods accessed with `this.methodName()` 
- ✅ Keep existing code structure 99% intact
- ✅ Don't create templates in javascript - keep in Blade 


## Current Petite Vue Files (in `resources/js/alpine/`)
- `bracket.js` - Tournament bracket management with Firebase
- `participant.js` - Participant profile editing
- `organizer.js` - Organizer profile editing
- `chat.js` - Chat functionality
- `notifications.js` - Notification system
- `settings.js` - User settings
- `teamSelect.js` - Team selection
- `teamhead.js` - Team management
- `beta.js` - Beta features

## Migration Strategy

### 1. Replace Petite Vue with Vue 3

**Current (package.json):**
```json
"dependencies": {
  "petite-vue": "^0.4.1"
}
```

**New:**
```json
"dependencies": {
  "vue": "^3.4.0"
}
```

**Run:**
```bash
npm install vue@3
npm uninstall petite-vue
npm run build
```

### 2. Update Imports (Only Change)

**Find and Replace Across All Files:**
```javascript
// Before
import { createApp, reactive } from "petite-vue";

// After
import { createApp, reactive } from "vue";
```

That's it! Your `createApp().mount()` code stays identical.

### 3. Component Conversion Pattern

**Current Petite Vue Pattern:**
```javascript
// bracket.js
import { createApp, reactive } from "petite-vue";

const fileStore = reactive({
  disputeClaimFiles: [],
  addFiles(newFiles, fileType) {
    if (fileType === 'claim') {
      this.disputeClaimFiles = [...this.disputeClaimFiles, ...newFiles];
    }
  }
});

function BracketData({ fileStore, bracketData, auth, db }) {
  return {
    reportId: null,
    isLoading: false,
    submitReport() {
      // logic
    }
  };
}

createApp({
  BracketData: () => BracketData({ fileStore, bracketData, auth, db }),
  UploadData: (type) => UploadData(type, fileStore)
}).mount('#Bracket');
```

**Vue 3 Options API Migration (95% Same):**
```javascript
// bracket.js
import { createApp, reactive } from "vue"; // ← Only change

// Keep reactive() exactly the same
const fileStore = reactive({
  disputeClaimFiles: [],
  addFiles(newFiles, fileType) {
    if (fileType === 'claim') {
      this.disputeClaimFiles = [...this.disputeClaimFiles, ...newFiles];
    }
  }
});

// Convert function to Options API object
function BracketData({ fileStore, bracketData, auth, db }) {
  return {
    // Split into data() and methods
    data() {
      return {
        reportId: null,
        isLoading: false
      };
    },
    methods: {
      submitReport() {
        // logic stays the same, access with this.reportId
      }
    }
  };
}

// createApp() stays IDENTICAL
createApp({
  BracketData: () => BracketData({ fileStore, bracketData, auth, db }),
  UploadData: (type) => UploadData(type, fileStore)
}).mount('#Bracket');
```

### 4. Blade Template Changes

**Current Petite Vue:**
```html
<div id="Bracket" v-scope>
  <div v-scope="BracketData()" @vue:mounted="init()">
    <button @click="submitReport">Submit</button>
    <div v-if="isLoading">Loading...</div>
  </div>

  <div v-scope="UploadData('response')" id="responseId" @vue:mounted="init2()">
    <!-- upload UI -->
  </div>
</div>
```

**Vue 3 Options API (Remove v-scope, keep everything else):**
```html
<div id="Bracket">
  <div>
    <button @click="submitReport">Submit</button>
    <div v-if="isLoading">Loading...</div>
  </div>

  <div id="responseId">
    <!-- upload UI -->
  </div>
</div>
```

**Move @vue:mounted logic to mounted() hook in JS:**
```javascript
createApp({
  BracketData: () => ({
    data() { return { /* ... */ }; },
    methods: { /* ... */ },
    mounted() {
      this.init(); // Move @vue:mounted="init()" here
    }
  })
}).mount('#Bracket');
```

### 5. Example: UploadData.js Migration

**Current (Petite Vue function):**
```javascript
export default function UploadData(type, fileStore) {
  return {
    get inputFiles() {
      return fileStore.getFiles(type)
    },
    handleFiles(event) {
      const newFiles = Array.from(event.target?.files);
      fileStore.addFiles(newFiles, type);
    },
    clearAllValues() {
      fileStore.clearAllFiles(type);
    },
    init2() {
      window.addEventListener('clearUploadData', () => {
        this.clearAllValues();
      });
    }
  };
}
```

**Vue 3 Options API (Split data/computed/methods):**
```javascript
export default function UploadData(type, fileStore) {
  return {
    // No data needed, all state in fileStore
    computed: {
      inputFiles() {
        return fileStore.getFiles(type);
      }
    },
    methods: {
      handleFiles(event) {
        const newFiles = Array.from(event.target?.files);
        fileStore.addFiles(newFiles, type);
      },
      clearAllValues() {
        fileStore.clearAllFiles(type);
      }
    },
    mounted() {
      window.addEventListener('clearUploadData', () => {
        this.clearAllValues();
      });
    }
  };
}
```

### 6. Example: bracket.js Full Migration

**Before:**
```javascript
import { createApp, reactive } from "petite-vue";

const fileStore = reactive({
  disputeClaimFiles: [],
  disputeResponseFiles: []
});

function CountDown(options) {
  return {
    targetDate: null,
    dateText: null,

    init3() {
      this.targetDate = options.targetDate;
      this.dateText = diffDateWithNow(this.targetDate);
      this.startTimer();
    },

    startTimer() {
      this.intervalId = setInterval(() => {
        this.dateText = diffDateWithNow(this.targetDate);
      }, 60000);
    }
  };
}

createApp({
  BracketData: () => BracketData({ fileStore, bracketData, auth, db }),
  UploadData: (type) => UploadData(type, fileStore),
  CountDown
}).mount('#Bracket');
```

**After (Vue 3 Options API):**
```javascript
import { createApp, reactive } from "vue"; // ← Only import change

// Keep reactive() the same
const fileStore = reactive({
  disputeClaimFiles: [],
  disputeResponseFiles: []
});

function CountDown(options) {
  return {
    data() {
      return {
        targetDate: null,
        dateText: null,
        intervalId: null
      };
    },
    methods: {
      startTimer() {
        this.intervalId = setInterval(() => {
          this.dateText = diffDateWithNow(this.targetDate);
        }, 60000);
      }
    },
    mounted() {
      this.targetDate = options.targetDate;
      this.dateText = diffDateWithNow(this.targetDate);
      this.startTimer();
    }
  };
}

// createApp() stays IDENTICAL
createApp({
  BracketData: () => BracketData({ fileStore, bracketData, auth, db }),
  UploadData: (type) => UploadData(type, fileStore),
  CountDown
}).mount('#Bracket');
```

### 7. Example: participant.js Migration

**Before:**
```javascript
import { createApp } from "petite-vue";

function ParticipantData() {
  return {
    isEditMode: false,
    user: { ...userData },
    participant: { ...participantData },

    changeFlagEmoji(event) {
      this.participant.region = event.target.value;
    },

    async submitEditProfile(event) {
      window.showLoading();
      const response = await fetch(url, { /* ... */ });
    },

    init() {
      this.fetchCountries();
    }
  };
}

document.addEventListener('DOMContentLoaded', () => {
  createApp({
    ParticipantData,
    ProfileData,
    ActivityLogs
  }).mount('#app');
});
```

**After (Vue 3 Options API):**
```javascript
import { createApp } from "vue"; // ← Only import change

function ParticipantData() {
  return {
    data() {
      return {
        isEditMode: false,
        user: { ...userData },
        participant: { ...participantData }
      };
    },
    methods: {
      changeFlagEmoji(event) {
        this.participant.region = event.target.value;
      },
      async submitEditProfile(event) {
        window.showLoading();
        const response = await fetch(url, { /* ... */ });
      },
      async fetchCountries() {
        // Same logic
      }
    },
    mounted() {
      this.fetchCountries();
    }
  };
}

document.addEventListener('DOMContentLoaded', () => {
  createApp({
    ParticipantData,
    ProfileData,
    ActivityLogs
  }).mount('#app');
});
```

## Migration Checklist by File

### `bracket.js` (Most Complex)
- [x] Change import: `"petite-vue"` → `"vue"`
- [x] Keep `reactive()` fileStore as-is
- [x] Split function returns into `data()` and `methods`
- [x] Move `@vue:mounted` logic to `mounted()` hook
- [x] Keep `createApp().mount()` identical
- [ ] Remove `v-scope` from blade templates

### `participant.js`
- [x] Change import: `"petite-vue"` → `"vue"`
- [x] Split `ParticipantData()` into `data()` and `methods`
- [x] Move `init()` call to `mounted()`
- [x] Keep `createApp().mount('#app')` identical
- [ ] Remove `v-scope` from blade templates

### `organizer.js`
- [x] Change import: `"petite-vue"` → `"vue"`
- [x] Split `OrganizerData()` into `data()` and `methods`
- [x] Move `init()` to `mounted()`
- [x] Keep phone validation (intlTelInput) in `mounted()`

### `UploadData.js` (Shared)
- [x] Change import: `"petite-vue"` → `"vue"`
- [x] Convert getter to `computed` property
- [x] Move `init2()` logic to `mounted()`
- [x] Keep methods the same

### Other Files (`chat.js`, `notifications.js`, etc.)
- [ ] Same pattern: change import, split data/methods, move init to mounted()

## Key Differences Summary

| Feature | Petite Vue | Vue 3 Options API |
|---------|------------|-------------------|
| Import | `from "petite-vue"` | `from "vue"` |
| State | Mixed in return object | `data() { return {} }` |
| Methods | Mixed in return object | `methods: {}` |
| Computed | Getter function | `computed: {}` |
| Lifecycle | `@vue:mounted` in template | `mounted()` hook |
| Reactive | `reactive({})` | `reactive({})` ✅ Same |
| CreateApp | `createApp().mount()` | `createApp().mount()` ✅ Same |
| Access | `this.property` | `this.property` ✅ Same |

## Quick Reference: Function → Options API

**Petite Vue Function:**
```javascript
function MyComponent(props) {
  return {
    // Properties become data
    count: 0,
    name: 'John',

    // Getters become computed
    get fullName() {
      return this.name + ' Doe';
    },

    // Functions become methods
    increment() {
      this.count++;
    },

    // init becomes mounted
    init() {
      console.log('mounted');
    }
  };
}
```

**Vue 3 Options API:**
```javascript
function MyComponent(props) {
  return {
    data() {
      return {
        count: 0,
        name: 'John'
      };
    },
    computed: {
      fullName() {
        return this.name + ' Doe';
      }
    },
    methods: {
      increment() {
        this.count++;
      }
    },
    mounted() {
      console.log('mounted');
    }
  };
}
```

## Benefits of Vue 3 Options API

1. **Minimal Code Changes**: ~95% of logic stays the same
2. **No New Syntax**: Same `this.property` access as Petite Vue
3. **Keep reactive()**: Your shared stores work identically
4. **Keep createApp()**: Mount logic unchanged
5. **Smaller Bundle**: 30% smaller than Vue 2
6. **Better Performance**: Faster rendering and updates
7. **Active Support**: Vue 2 reached EOL, Vue 3 is current
8. **DevTools**: Better debugging with Vue DevTools 6+
9. **Future Ready**: Can gradually adopt Composition API later
10. **TypeScript Ready**: Better type inference if needed

## Common Migration Patterns

### Pattern 1: Simple Data + Methods
```javascript
// Before (Petite Vue)
function MyForm() {
  return {
    email: '',
    password: '',
    async submit() {
      await fetch('/api/login', {
        body: JSON.stringify({ email: this.email })
      });
    }
  };
}

// After (Vue 3 Options API)
function MyForm() {
  return {
    data() {
      return {
        email: '',
        password: ''
      };
    },
    methods: {
      async submit() {
        await fetch('/api/login', {
          body: JSON.stringify({ email: this.email })
        });
      }
    }
  };
}
```

### Pattern 2: Shared Reactive Store
```javascript
// Before and After are IDENTICAL
import { reactive } from "vue"; // Only import changes

const store = reactive({
  items: [],
  addItem(item) {
    this.items.push(item);
  }
});

// Use in component - no changes needed
function MyComponent() {
  return {
    methods: {
      add() {
        store.addItem('new');
      }
    }
  };
}
```

### Pattern 3: Computed Properties
```javascript
// Before (Petite Vue)
function Cart() {
  return {
    items: [],
    get total() {
      return this.items.reduce((sum, item) => sum + item.price, 0);
    }
  };
}

// After (Vue 3 Options API)
function Cart() {
  return {
    data() {
      return {
        items: []
      };
    },
    computed: {
      total() {
        return this.items.reduce((sum, item) => sum + item.price, 0);
      }
    }
  };
}
```

## Blade Template Migration

### Remove v-scope Attributes

**Before:**
```html
<div id="Bracket" v-scope>
  <div v-scope="BracketData()" @vue:mounted="init()">
    <button @click="submitReport">Submit</button>
  </div>

  <div v-scope="CountDown({ targetDate: '2024-12-31' })" @vue:mounted="init3()">
    {{ dateText }}
  </div>
</div>
```

**After:**
```html
<div id="Bracket">
  <div>
    <button @click="submitReport">Submit</button>
  </div>

  <div>
    {{ dateText }}
  </div>
</div>
```

All `@click`, `v-if`, `v-for`, `{{ }}` syntax stays identical.

## Recommended Migration Order

1. **Start with smallest file**: `beta.js` or `settings.js`
2. **Then shared utility**: `UploadData.js` (used by multiple pages)
3. **Then simple forms**: `participant.js` and `organizer.js`
4. **Finally complex**: `bracket.js` (Firebase + multiple components)
5. **Test each file** before moving to next

## Testing After Migration

- [ ] `npm run build` completes without errors
- [ ] All forms submit correctly
- [ ] File uploads work (UploadData component)
- [ ] Firebase real-time updates work (bracket.js)
- [ ] Modal interactions work
- [ ] Countdown timers update (CountDown component)
- [ ] No console errors in browser
- [ ] Vue DevTools shows component hierarchy

## Vite Configuration

**No changes needed!** Vite supports Vue 3 by default.

Your existing `vite.config.js` should work as-is:
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: true
    })
  ]
});
```

## Why NOT Use Composition API?

You asked for minimal changes. The Composition API requires:
- ❌ Wrapping everything in `setup()`
- ❌ Using `ref()` and `.value` everywhere
- ❌ Manual `watch()` for side effects
- ❌ Different mental model
- ❌ More code changes

**Options API keeps your existing code structure with minimal edits.**

## Final Summary

**Total Changes Required:**
1. ✅ `package.json`: Change `"petite-vue"` → `"vue"`
2. ✅ All JS files: Change import from `"petite-vue"` → `"vue"`
3. ✅ Split function returns: `{ data() {}, methods: {}, mounted() {} }`
4. ✅ Blade templates: Remove `v-scope` attributes
5. ✅ Move `@vue:mounted` → `mounted()` hook in JS

**That's it! ~95% of your code stays the same.**

---

**Ready to migrate? Start with the smallest file and work your way up.**

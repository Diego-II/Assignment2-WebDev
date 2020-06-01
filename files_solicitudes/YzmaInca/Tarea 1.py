#!/usr/bin/env python
# coding: utf-8

# # **Tarea 1:** EDA y modelos bayesianos
# ## **Grupo 5** 
# ## **Integrantes:** 
#  * Diego Irarrazaval
#  * Pablo Paredes
#  * Tomas Rojas

# ## Pregunta 1:  Carga y limpieza de datos.
# ### P1.1

# In[ ]:


import pandas as pd
import glob as glob
import numpy as np


# `files_raw`, `files_estadisticas` y `files_asignacion` son listas que contienen las direcciones donde se encuentran los .csv a leer. 

# In[ ]:


files_raw = glob.glob('data/raw/**/*.csv', recursive = True)
files_estadisticas = glob.glob('data/estadisticas_upz/*.csv')
files_asignacion = glob.glob('data/asignacion_upz/*.csv')

data_raw = ([pd.read_csv(dir) for dir in files_raw])


# ### Creación del DataFrame y reporte de archivos furnished

# In[ ]:


'''
Creamos un data frame 'furnished', el cual tendrá dos columnas
1) 'url' para hacer el merge finalmente y obtener el data frame requerido
2) 'furnished' para contar cuantos datos están en archivos furnished y no en archivos all
'''

data_all = []
data_fur = []

for i in [0,2,4,6,8]:
    
    df1 = pd.read_csv(files_raw[i])
    df2 = pd.read_csv(files_raw[i+1])
    
    data_all.append(df1)
    data_fur.append(df2)
    
df_all = pd.concat(data_all)
df_fur = pd.concat(data_fur)
    
f1 = pd.merge(df_all, df_fur, how='outer', on='url', indicator='furnished')
furnished = f1[['url', 'furnished']].copy()

furnished.drop_duplicates(inplace = True)
furnished.reset_index(drop=True, inplace=True)

# Se reportan si hay datos de archivos furnished que no estén en all 

print('Hay '+ str(len(furnished[furnished['furnished'] == 'right_only'])) + ' datos de archivos furnished que no estan en all')


# In[ ]:


'''
Creamos el data frame 'data'
'''

df_aux = pd.concat([df_all, df_fur], ignore_index=True)

data = pd.merge(df_aux, furnished, how='inner', on='url')
data.drop_duplicates(inplace=True)
data.reset_index(drop=True, inplace=True)

# Se elimina la columna 'furnished' y se quitan los duplicados

data.drop('furnished', axis=1, inplace=True)
data.drop_duplicates(inplace=True)
data.reset_index(drop=True, inplace=True)


# ### P1.2 Limpieza de Columnas 

# In[ ]:


'''
Limpieza de columnas 'price', 'surface', 'n_rooms', 'n_bath'
'''

# Columna de precio ('price') tipo float

data.price = data['price'].str.replace('.', '')
data.price = data['price'].str.strip('$')
data.price = data['price'].map(float)

# Columna de área ('surface') tipo float

data.surface = data['surface'].replace('m2', '', regex=True)
data.surface = data['surface'].map(float)


# In[ ]:


# Notamos que en la columna de dormitorios ('n_rooms') existe la opción '5+'
# por lo que dejaremos esta columna como categórica

data.n_rooms.unique()


# In[ ]:


# Se crea un diccionario para pasar los datos numéricos de 'n_rooms' a string
# y se efectúa el mapeo

dic = {1.0: '1', 2.0:'2', 3.0: '3', 4.0:'4', 5.0:'5'}

data.n_rooms = data['n_rooms'].replace(dic)


# In[ ]:


# Como habían datos que solo se diferenciaban en la cantidad de dormitorios
# por el tipo de dato que eran (float o int), puede haberse creado duplicados. Se borran nuevamente los duplicados
# de data

data.drop_duplicates(inplace=True)
data.reset_index(drop=True, inplace=True)


# In[ ]:


# Se hace lo mismo con la columnna de cantidad de baños ('n_bath')

data.n_bath.unique()


# In[ ]:


dic = {1.0: '1', 2.0:'2', 3.0: '3', 4.0:'4', 5.0:'5'}

data.n_bath.replace(dic)
data.drop_duplicates(inplace=True)
data.reset_index(drop=True, inplace=True)


# In[ ]:


'''
Separación de columna property_tipe|rent_type|location en tres columnas
con los nombres respectivos

'''

# Renombramos la columna

data.columns = ['PTL', 'price', 'n_rooms', 'n_bath', 'surface', 'details', 'url', 'metrocuadrado_index']


# In[ ]:


# Creamos las columnas y las llenamos

col = data['PTL'].str.split(', ', expand=True)

meta_col = col[0].str.split(' en ', expand=True)

# Nos aseguramos que hayan solo las siguientes opciones:
# -> 'Casa', 'Apartamento' para property_type
# -> 'Arriendo', 'Venta Y Arriendo' para rent_type

print(meta_col[0].unique())
print(meta_col[1].unique())


# In[ ]:


# Formamos las nuevas columnas 'property_type', 'rent_type', 'location'

data['property_type'] = meta_col[0] 
data['rent_type'] = meta_col[1]
data['location'] = col[1]

# y retiramos la columna PTL

data.drop('PTL', axis=1, inplace=True)


# In[ ]:


# Finalmente quitamos la ciudad de 'location'

loc = col[1].str.split(' Bogotá', expand=True)
data.location = loc[0]


# ### P1.3 Precio por metro cuadrado y Cantidad de garages

# In[ ]:


'''
Agregamos una columna que represente el precio por metro cuadrado 'price_per_m2'

'''

data['price_per_m2'] = np.where(data['surface'] <= 0, float('nan'), data['price']/data['surface'])


# In[ ]:


'''
Obtenemos la cantidad de garajes y lo agregamos como columna también 'cant_garajes'

'''

garajes_list = data.url.str.split('-garajes', expand=True)
garajes_num = garajes_list[0].str.rsplit('-', n=1, expand=True)

# indices que tienen urls con info de la cantidad de garajes
ind = garajes_list[1].index[garajes_list[1].isna() == False]

# generación de nueva columna para después asignarla a la data
garajes_list[2] = np.nan
garajes_list[2].loc[ind] = garajes_num[1].loc[ind]

# agregación de la cantidad de garajes a la data (nan si no hay info)
data['cant_garajes'] = garajes_list[2]


# ### P1.4 Clasificación Tipo de Producto

# In[ ]:


'''
Creamos una nueva columna 'clasif_prod_type' donde se representará la clasificación
de la vivienda con dígitos del 1 al 8 de acuerdo al enunciado

'''

data['clasif_prod_type'] = np.nan

data.clasif_prod_type.loc[(data.property_type == 'Casa') & (data.surface >= 80) & (data.surface < 120)] = 1
data.clasif_prod_type.loc[(data.property_type == 'Casa') & (data.surface >= 120) & (data.surface < 180)] = 2
data.clasif_prod_type.loc[(data.property_type == 'Casa') & (data.surface >= 180) & (data.surface < 240)] = 3
data.clasif_prod_type.loc[(data.property_type == 'Casa') & (data.surface >= 240) & (data.surface < 360)] = 4
data.clasif_prod_type.loc[(data.property_type == 'Casa') & (data.surface >= 360) & (data.surface < 460)] = 5
data.clasif_prod_type.loc[(data.property_type == 'Apartamento') & (data.surface >= 40) & (data.surface < 60)] = 6
data.clasif_prod_type.loc[(data.property_type == 'Apartamento') & (data.surface >= 60) & (data.surface < 80)] = 7
data.clasif_prod_type.loc[(data.property_type == 'Apartamento') & (data.surface >= 80) & (data.surface < 120)] = 8

data['clasif_prod_type'].astype('category')


# ### P1.5 Obtención del código UPZ

# In[ ]:


'''
Se carga el archivo y se guarda en un dataframe data_upz
Luego, se realiza el merge para obtener el código para cada barrio

'''

# Se guarda la base de datos en un data frame
data_upz = pd.read_csv(files_asignacion[0], usecols = ['UPlCodigo', 'pro_location', 'UPlArea'])

# Se deja todo en minuscula para poder hacer el merge correctamente
data_upz.pro_location = data_upz.pro_location.map(str).map(lambda s: s.lower())
data.location = data.location.map(lambda s: s.lower())


# In[ ]:


#Se realiza el merge y se reportan cuantos datos no fueron asignados con código UPZ

data_merge = pd.merge(data, data_upz, left_on='location', right_on='pro_location', how='left')

print('A ' + str(sum(data_merge.UPlCodigo.isna())) + ' datos no se les puede asignar código UPZ')
print('lo cual es ' + str(sum(data_merge.UPlCodigo.isna())/len(data)*100) + '% de los datos')


# In[ ]:


# Creamos la columna 'UPZ' en data y le asignamos el código UPZ obtenido en data_merge

data['UPZ'] = data_merge['UPlCodigo']
data['UPZ_area'] = data_merge['UPlArea']


# ### P1.6 Fusión de datos con código UPZ

# In[ ]:


'''
Se cargan los datos en distintos data frames, para después hacerles merge con data
Luego, se crea una columna de densidad poblacional para cada código UTZ

'''

# Se cargan los datos en data frames respectivamente

data_pobl = pd.read_csv(files_estadisticas[0])
data_inseg = pd.read_csv(files_estadisticas[1])
data_verde = pd.read_csv(files_estadisticas[2])


# In[ ]:


# Se quitan las columnas innecesarias de data_pobl y data_inseg

data_pobl.drop(['Unnamed: 0', 'nomupz'], axis=1, inplace=True)
data_inseg.drop(['Unnamed: 0', 'UPlNombre2'], axis=1, inplace=True)


# In[ ]:


# En data_verde se tiene que al código UPZ viene solo el número
# por lo tanto, hay que transformarlo al formato UPZ + número para poder hacer el merge correctamente

col = data_verde.cod_upz.map(int).map(str)
col_upz = 'UPZ' + col

data_verde.cod_upz = col_upz

# Se eliminan las columnas innecesarias
data_verde.drop(['Unnamed: 0', 'upz'], axis=1, inplace=True)


# In[ ]:


# Se realiza el merge, eliminando después las columnas innecesarias

data = pd.merge(data, data_pobl, left_on='UPZ', right_on='upz', how='left')
data.drop('upz', axis=1, inplace=True)
data = pd.merge(data, data_inseg, left_on='UPZ', right_on='UPlCodigo', how='left')
data.drop('UPlCodigo', axis=1, inplace=True)
data = pd.merge(data, data_verde, left_on='UPZ', right_on='cod_upz', how='left')
data.drop('cod_upz', axis=1, inplace=True)


# In[ ]:


# Finalmente, se crea la columna de densidad de población para cada código UTZ ('UTZ_density')

data['UTZ_density'] = data.personas/data.UPZ_area


# ## P2. EDA
# ### P2.1 Creacion de `estilo()`

# In[ ]:


import seaborn as sns
import matplotlib.pyplot as plt
#se crea diccionario que dará los valores a setear por defecto en el notebook
custom = {
    "font.size": 12,
    "axes.labelsize": 18,
    "axes.titlesize": 18,
    "xtick.labelsize": 18,
    "ytick.labelsize": 18,
    "legend.fontsize": 20,
    "axes.linewidth": 1.25,
    "grid.linewidth": 1,
    "lines.linewidth": 1.5,
    "lines.markersize": 6,
    "patch.linewidth": 1,
    "xtick.major.width": 1.25,
    "ytick.major.width": 1.25,
    "xtick.minor.width": 1,
    "ytick.minor.width": 1,
    "xtick.major.size": 6,
    "ytick.major.size": 6,
    "xtick.minor.size": 4,
    "ytick.minor.size": 4,
    'figure.figsize':(10.,8.),
    "figure.facecolor": "white",
    "axes.labelcolor": ".15",
    "xtick.direction": "out",
    "ytick.direction": "out",
    "xtick.color": ".15",
    "ytick.color": ".15",
    "axes.axisbelow": True,
    "grid.linestyle": "--",
    "text.color": ".1",
    "patch.force_edgecolor": True,
    "image.cmap": "RdBu_r",
    "xtick.top": False,
    "ytick.right": False,
         }

#En las siguiente línea se implementa el diccionario personalizado como default para este notebook en seaborn
sns.set(rc=custom)

#se escoge una de las paletas que vienen con seaborn 
#(distinta a la que se usa por defecto) para el resto de notebook.
#sns.set_palette('Set2')


# ### P2.2 Perfilamiento: 
# #### Naturaleza de las variables:
# Antes del perfilamiento se hara un estudio de la naturaleza de las variables y se observaran algunas columnas 
# para mejor entendimiento de los datos. 
# Adicionalmente a estos estudios, se realizo lo siguiente:
# ```
# import pandas_profiling as pp
# pp.ProfileReport(data)
# 
# profile = data.profile_report(title='Pandas Profiling Report')
# profile.to_file(output_file="output.html")
# ```
# No se incluye en el notebook por el tipo de output que genera y debido a que tiene un largo tiempo de ejecucion. 
# 
# En primer lugar, es importante conocer que tipos de datos tienen las variables (o columnas).

# In[ ]:


data.info()


# Para comprender mejor las variables, se visualizan las primeras 5 filas con `head()`:

# In[ ]:


data.head()


# Para ayudar a entender las variables numericas:

# In[ ]:


data.describe()


# #### Agrupacion por naturaleza:
# A continuacion, se grafican las variables para entender como se distribuyen. Para esto, separamos las variables categoricas de las numericas. Ademas, debido a que las variables `'details','url','UPZ','location'` tienen muchos valores distintos, se dejaran en una categoria aparte:

# In[ ]:


names = ['numeric', 'categorical']

categorical = [col for col in data.columns if data[col].dtype == 'O']
categorical += set(['clasif_prod_type'])

others = ['details','url','UPZ','location']

numeric = list(set(data.columns) - set(categorical))

for col in others:
    categorical.remove(col)

mapping = [('numeric', col) for col in numeric]
mapping.extend([('categorical', col) for col in categorical])
mapping.extend([('others', col) for col in others])
'''
Se reordenan las columnas del dataframe para que coincidan con el esquema 
del multi indice
'''

data = data.reindex(columns = numeric + categorical + others)


# #### Graficos de distribuciones univariadas de las variables:
# Para esto, se implementan dos funciones: `plot_numeric_vars` y `plot_categorical_vars`. 

# In[ ]:


#Funcion para graficar Distribuciones univariadas de variables numericas:
def plot_numeric_vars(df, columns, title):
    '''
    Creaciion de graficos de Distribuciones Univariadas, recibe 
    el dataframe, las columnas a graficar y el 'SuperTitulo'    
    
    Args:
    ----------
    columns: list
        Lista con los nombres de las columnas de tipo numerico a graficar. 
        
    title: String
        Titulo
        
    Returns: None
        Se muestran los graficos
    
    
    Ejemplo de uso: 
    ------------
    
    Dado un DataFrame df:
    col_a_graficar = ['col1','col2','col3']
    
    plot_uni_dist(df,col_a_graficar,'Grafico de variables 1, 2, 3')
    
    '''
    
    nplots = len(columns)
    ncols = 3
    nrows = int(np.ceil(nplots/ncols))
    
    # Grilla de subplots
    fig, ax = plt.subplots(nrows=nrows, ncols=ncols, figsize=[17, 17])

    #Veamos si se deben remover plot:
    if nplots - ncols*nrows > 0:
        r = -(nplots - ncols*nrows)
        list(map(lambda a : a.remove(), ax[-1,r:]))
    
    fig.tight_layout()
    
    #Ponemos el titulo:
    fig.suptitle(title,
             fontsize=20,
             x=0.5,
             y=1.05)
    
    #Se recorre cada axis, para cada columna del dataframe, se genera un grafico 
    #distinto en funcion del tipo de dato.
    for axis, col in zip(ax.flatten(), columns):
        try :
            # Graficos para datos numericos
            sns.distplot(df[(col)], ax=axis, rug=True)

        except RuntimeError:
            sns.distplot(df[(col)], ax=axis, rug=True, kde=False)

        axis.set_xlabel(col, fontsize=15)

    # Se ajusta el espaciado interno entre subplots
    w, h = (.4, .4)
    plt.subplots_adjust(wspace=w, hspace=h)
    
    


# In[ ]:


#Funcion para graficar Distribuciones univariadas de variables numericas:
def plot_categorical_vars(df, columns, title, order = dict()):
    '''
    Creaciion de graficos de Distribuciones Univariadas recibe
    el dataframe, las columnas a graficar y el 'SuperTitulo'    
    
    Args:
    ----------
    columns: list
        Lista con los nombres de las columnas de tipo numerico a graficar. 
        
    title: String
        Titulo
        
    order: dict
        Diccionario que contiene como llave el nombre de la columna que se desea ordenar 
        y como valor una lista con las categorias correspondientes a esa columna en el orden 
        deseado. 
        
    Returns: None
        Se muestran los graficos
        
        
    Ejemplo de uso: 
    -------------
    Dado un DataFrame df:
    col_a_graficar = ['col1','col2','col3']
    cat_order = {
         'col1' = ['cat1','cat2','cat3', 'cat4']
         'col2' = [i for i in range(1,11)]
         'col3' = ['mujer', 'hombre', 'otro']
    }
    plot_uni_dist(df,col_a_graficar,'Grafico de variables categoricas', cat_order)
    -----------------
    
    Cuando no se incluye un diccionario order, se ordena por defecto. 
    
    
    '''
    
    nplots = len(columns)
    ncols = 3
    nrows = int(np.ceil(nplots/ncols))
    
    # Grilla de subplots
    fig, ax = plt.subplots(nrows=nrows, ncols=ncols)#, figsize=[17, 17])

    #Veamos si se deben remover plot:
    if nplots - ncols*nrows > 0:
        r = -(nplots - ncols*nrows)
        list(map(lambda a : a.remove(), ax[-1,r:]))
    
    fig.tight_layout()
    
    #Ponemos el titulo:
    fig.suptitle(title,
             fontsize=20,
             x=0.5,
             y=1.05)
    '''
    Se recorre cada axis, para cada columna del dataframe, se genera un grafico 
    distinto en funcion del tipo de dato.

    '''
    for axis, col in zip(ax.flatten(), columns):    
        # Graficos para datos tipos str
        try:
            sns.countplot(df[(col)], ax=axis, order = order[col])
        except:
            sns.countplot(df[(col)], ax=axis)
        axis.set_axis_on()
        axis.set_title(col, fontsize=15)
  
    
    # Se ajusta el espaciado interno entre subplots
    h, w = (.8, .8)
    plt.subplots_adjust(wspace=w, hspace=h)


# In[ ]:


plot_numeric_vars(data,numeric,'Grafico variables numericas')


# In[ ]:


plot_order = {'n_rooms': [ '1', '2', '3', '4', '5', '5+'],
              'n_bath': [ '1', '2', '3', '4', '5', '5+'],
              'property_type': ['Apartamento', 'Casa'],
              'rent_type': ['Arriendo', 'Venta Y Arriendo'],
              'cant_garajes': ['1', '2', '3', '4', '4+'],
              'clasif_prod_type': [i for i in range(1,9)]
             }
plot_categorical_vars(data,categorical,'Grafico variables categoricas',plot_order)
#len(categorical)


# In[ ]:


data[others].describe()


# Como la variable _target_ es el precio cuadrado por metro cuadrado, a continuacion se implementa una funcion para Estudiar como se relacionan estas variables. Para las variables categoricas, el tipo de grafico utilizado es el boxplot o el grafico de violin. Para las variables numericas se utiliza el _scatter plot_. Dado el rango de precio por $metro^2$, es importante que exista la opcion de escalar el eje y.

# In[ ]:


def plot_violin(target, col, scale = True ,df = data):
    '''
    Para variables categoricas v/s una variable target. Las variables categoricas deben ser de baja
    cardinalidad, sino el grafico no es util.
    Genera grafico de violin e histograma con distribucion de la variable a comparar.
    Recibe el nombre de la variable objetivo y la variablecon la cual se desea comparar.
    
    Args:
    --------
    target: String
        Nombre de la columna objetivo (eje y).
    col: String 
        Nombre de la columna que representara el eje x.
    scale: Bool
        Si se quiere o no escalar el eje y. Util cuando el rango de esta variable es muy grande. 
        
    Returns: None
        Muestra en pantalla dos graficos: Violin plot (similar al boxplot) y el histograma de 
        la variable col.
        
    Ejemplo de uso:
    --------------
    plot_violin('price_per_m2','n_rooms')
    '''

    # Sirve para fija el tamaño de lasetiquetas del plot
    fontdict = {'fontsize':20}

    # Estrucutra de figura y axes
    fig, ax = plt.subplots(2,1,figsize=[12,13])

    # violin plot --> equivalente a catplot(kind = 'violin')
    if scale:
        sns.violinplot(col,
                    y=(target),
                    data=df,
                    kind='violin',
                    ax=ax[0]).set_yscale("log")
    else:
        sns.violinplot(col,
            y=(target),
            data=df,
            kind='violin',
            ax=ax[0])

    sns.countplot(df[col], ax=ax[1])

    ax[0].set_xlabel(col, fontdict)
    ax[1].set_xlabel(col, fontdict)

    ax[0].set_ylabel(target, fontdict)
    title = 'Violin plot ' + col + ' v/s ' + target
    ax[0].set_title(title, fontdict)
    title_y = "Frecuencias " + col
    ax[1].set_title(title_y, fontdict)
    

    h, w = (.3, .1)
    plt.subplots_adjust(wspace=w, hspace=h)


# In[ ]:


plot_violin('price_per_m2','n_rooms')


# In[ ]:


plot_violin('price_per_m2','clasif_prod_type')


# In[ ]:


plot_violin('price_per_m2','property_type')


# In[ ]:


plot_violin('price_per_m2','cant_garajes')


# Ya habiendo visto algunas de las variables categoricas v/s el precio por metro cuadrado, corresponde hacer el mismo analisis con las variables numericas:

# In[ ]:


def plot_scatter(target, col, reg = True, scale = False, df = data):
    '''
    Para variables numericas v/s una variable target. 
    Genera el scatterplot e histograma con distribucion de la variable a comparar.
    Recibe el nombre de la variable objetivo y la variablecon la cual se desea comparar.
    
    Args:
    --------
    target: String
        Nombre de la columna objetivo (eje y).
    col: String 
        Nombre de la columna que representara el eje x.
    reg: Bool
        Si se incluye o no una regresion lineal sobre los datos.
    scale: Bool
        Si se quiere o no escalar el eje y. Util cuando el rango de esta variable es muy grande. 
        
    Returns: None
        Muestra en pantalla dos graficos: Violin plot (similar al boxplot) y el histograma de 
        la variable col.
        
    Ejemplo de uso:
    --------------
    plot_scatter('price_per_m2','price')
    '''
    # Sirve para fija el tamaño de lasetiquetas del plot
    fontdict = {'fontsize':20}

    # Estrucutra de figura y axes
    fig, ax = plt.subplots(2,1,figsize=[10,10])

    # violin plot --> equivalente a catplot(kind = 'violin')
    if scale:
        if reg:
            sns.regplot(x = col,
                        y= target,
                        data=df,
                        ax=ax[0]).set_yscale("log")
            title = 'Scatter plot ' + col + ' v/s log(' + target + ') con ajuste lineal.'
            ax[0].set_title(title, fontdict,y=1.05)
        else:
            sns.scatterplot(x = col,
                            y=target,
                            data=df,
                            ax=ax[0]).set_yscale("log")
            title = 'Scatter plot ' + col + ' v/s log(' + target + ')'
            ax[0].set_title(title, fontdict,y=1.05)
    else:
        if reg:
            sns.regplot(x = col,
                        y= target,
                        data=df,
                        ax=ax[0])
            title = 'Scatter plot ' + col + ' v/s ' + target + ' con ajuste lineal.'
            ax[0].set_title(title, fontdict,y=1.05)
        else:
            sns.scatterplot(x = col,
                            y=target,
                            data=df,
                            ax=ax[0])
            title = 'Scatter plot ' + col + ' v/s ' + target
            ax[0].set_title(title, fontdict ,y=1.05)

    sns.distplot(df[col], ax=ax[1])

    ax[0].set_xlabel(col, fontdict)
    ax[1].set_xlabel(col, fontdict)

    ax[0].set_ylabel(target, fontdict)
    
    title_y = "Frecuencias " + col
    ax[1].set_title(title_y, fontdict,y=1.05)
    

    h, w = (.6, .1)
    plt.subplots_adjust(wspace=w, hspace=h)


# A continuacion se muestan los _scatterPlot_ de algunas variables de interes v/s el precio por metro cuadrado. Se observan las variables `metrocuadrado_index`, `indice_envegecimiento`, `UPZ_area`, `UTZ_density` y `jefe_mujer_perc` ya que se observa que son variables poco concentradas:

# In[ ]:


var_de_interes = ['metrocuadrado_index', 'indice_envegecimiento', 'UPZ_area', 'UTZ_density', 'jefe_mujer_perc']
for var in var_de_interes:
    plot_scatter('price_per_m2', var)


# Notemos que, si hay alguna relacion lineal entre alguna de las variables graficadas y el precio por metro cuadrado, es muy dificil observarla. Esto ya que hay algunos puntos del precio por m2 que escapan de la barrera de los 100000. Para esto, observemos que ocurre cuando limitamos el precio maximo del metro cuadrado a 100000. 

# In[ ]:


new_data = data.copy()
new_data = new_data[new_data.price_per_m2 < 100000]
'''De esta forma tambien es posible eliminar outliers.'''
#new_data = new_data.nsmallest(18000,'price_per_m2')
print('Largo original del DataFrame: ',len(data))
print('Largo del nuevo DataFrame: ', len(new_data))
print('Maximo precio por m2 del nuevo DataFrame: ', max(new_data.price_per_m2))


# In[ ]:


for var in var_de_interes:
    plot_scatter('price_per_m2', var,scale=False,reg=True ,df = new_data)


# De esta forma, se observa una clara relacion lineal entre `metrocuadrado_index` y `price_per_m2`. Lo mismo con la variable `jefe_mujer_perc` e `indice_envegecimiento`. Estas tres seran de interes mas adelante. 

# #### Perfilamiento Bivariado:
# Para comenzar el perfilamiento bivariado se emplean visualizaciones a pares. De esta forma podemos observar las relaciones entre las distintas variables. 

# In[ ]:


categorical


# In[ ]:


interes = ['price_per_m2', 'metrocuadrado_index', 'indice_envegecimiento', 'UPZ_area', 'UTZ_density', 
           'jefe_mujer_perc', 'clasif_prod_type']


# In[ ]:


sns.pairplot(data = data[interes], diag_kind='kde')


# La primera columna nos muestra la relacion entre `price_per_m2` y las demas variables de interes. Al igual que antes, es dificil extraer informacion valiosa de relaciones entre variables a simple vista de los graficos. Para comprobar que esto no sea debido a los `price_per_m2` que escapaban los 100000, se realizara el mismo grafico con `new_data`:

# In[ ]:


sns.pairplot(data = new_data[interes], diag_kind='kde')


# Como en el caso del perfilamiento univariado de las variables numericas, trabajar con los datos correspondientes a `price_per_m2` menores a 100000 entrega informacion sobre las relaciones de las variables: En primer lugar, se vuelve a observar el comportamiento `price_per_m2` v/s `metrocuadrado_index`. Luego se observa que la variable `clasif_prod_type` se comporta de forma similar v/s todas las variables de interes. 
# 

# In[ ]:





# Para entender como se relacionan las variables entre ellas, es de utilidad el mapa de calor:

# In[ ]:


sns.heatmap(data.corr(), linewidths=.01, cmap = 'RdBu_r')


# # Juegos para feature selection.

# In[ ]:


from sklearn.feature_selection import SelectKBest
from sklearn.model_selection import train_test_split
from sklearn.feature_selection import f_regression
from sklearn.preprocessing import LabelBinarizer
from sklearn.preprocessing import StandardScaler
from sklearn.feature_selection import f_classif, chi2
from sklearn import metrics
from sklearn import svm


# In[ ]:


#car es la cantidad de caracteristicas que queremos
def selectFeatures(datos,etiquetas,car):
  return SelectKBest(f_regression, k=car).fit_transform(datos,etiquetas)

#Separamos en conjunto de entrenamiento, validacion y prueba
def getConjuntos(datos,label,por):
  return train_test_split(datos, label, test_size=por, random_state=42)


# In[ ]:


data_X_cols = [col for col in new_data.columns if col != 'price_per_m2']

data_X = new_data.dropna()

data_Y = data_X.price_per_m2

data_col = [col for col in numeric if col != 'price_per_m2']
data_X = data_X[data_col]

#data_x = selectFeatures(data_X, data_Y, 3)


# In[ ]:


data_x


# In[ ]:


# Create and fit selector
selector = SelectKBest(f_regression, k=5)
selector.fit(data_X, data_Y)

# Get columns to keep and create new dataframe with those only
cols = selector.get_support(indices=True)
features_df_new = data_X.iloc[:,cols]


# In[ ]:


features_df_new


# In[ ]:





# In[ ]:




